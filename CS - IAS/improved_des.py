"""
improved_des.py — Core IDES encryption and decryption.

Orchestrates 32 rounds of the Feistel network with IP, FP,
the extended key schedule, and dynamic S-boxes.

Inputs:  plaintext (16 hex chars = 64 bits), key (32 hex chars = 128 bits)
Outputs: ciphertext or recovered plaintext (16 uppercase hex chars)
"""

from utils import hex_to_bits, bits_to_hex, permute, xor_bits
from key_schedule import build_key_schedule, get_key_schedule_sha256_hex
from sbox_generator import build_sboxes, display_sboxes, get_sbox_master_seed_hex
from feistel import f_function, _bits48_to_hex


# Initial Permutation (IP): applied to the 64-bit plaintext block
IP_TABLE = [
    58, 50, 42, 34, 26, 18, 10,  2,
    60, 52, 44, 36, 28, 20, 12,  4,
    62, 54, 46, 38, 30, 22, 14,  6,
    64, 56, 48, 40, 32, 24, 16,  8,
    57, 49, 41, 33, 25, 17,  9,  1,
    59, 51, 43, 35, 27, 19, 11,  3,
    61, 53, 45, 37, 29, 21, 13,  5,
    63, 55, 47, 39, 31, 23, 15,  7,
]

# Final Permutation (FP = IP⁻¹): applied after all Feistel rounds
FP_TABLE = [
    40,  8, 48, 16, 56, 24, 64, 32,
    39,  7, 47, 15, 55, 23, 63, 31,
    38,  6, 46, 14, 54, 22, 62, 30,
    37,  5, 45, 13, 53, 21, 61, 29,
    36,  4, 44, 12, 52, 20, 60, 28,
    35,  3, 43, 11, 51, 19, 59, 27,
    34,  2, 42, 10, 50, 18, 58, 26,
    33,  1, 41,  9, 49, 17, 57, 25,
]

_DIVIDER      = "=" * 72
_ROUND_DIV    = "-" * 72
_THIN_DIV     = "·" * 72


def _print_key_material(key_hex: str, key_bits: list[int]) -> None:
    """
    Print the SHA-256 intermediate values produced by the key schedule
    and S-box generator for the given key.
    """
    ks_sha256  = get_key_schedule_sha256_hex(key_bits)
    sb_sha256  = get_sbox_master_seed_hex(key_bits)
    sbox_list  = build_sboxes(key_bits)

    print(_DIVIDER)
    print("  Key Material")
    print(_DIVIDER)
    print(f"  Master Key (128-bit)    : {key_hex.upper()}")
    print()
    print("  SHA-256 Outputs")
    print(_THIN_DIV)
    print(f"  Key-Schedule SHA-256")
    print(f"    SHA-256(PC1(K_L) || PC1(K_R))")
    print(f"    = {ks_sha256[:32]}")
    print(f"      {ks_sha256[32:]}")
    print()
    print(f"  S-Box Master Seed SHA-256")
    print(f"    SHA-256(128-bit key bytes)")
    print(f"    = {sb_sha256[:32]}")
    print(f"      {sb_sha256[32:]}")
    print()
    print("  Generated S-Boxes  (hex values, 4 rows × 16 cols each)")
    print(_THIN_DIV)
    display_sboxes(sbox_list)


def encrypt(plaintext_hex: str, key_hex: str, verbose: bool = False) -> str:
    """
    Encrypt a 64-bit plaintext block using the IDES algorithm.

    Args:
        plaintext_hex: exactly 16 hex characters (64 bits)
        key_hex:       exactly 32 hex characters (128 bits)
        verbose:       if True, print every round's intermediate values

    Returns:
        ciphertext as 16 uppercase hex characters
    """
    if len(plaintext_hex) != 16:
        raise ValueError(
            f"plaintext_hex must be exactly 16 hex chars, got {len(plaintext_hex)}"
        )
    if len(key_hex) != 32:
        raise ValueError(
            f"key_hex must be exactly 32 hex chars, got {len(key_hex)}"
        )

    plaintext_bits = hex_to_bits(plaintext_hex)
    key_bits       = hex_to_bits(key_hex)

    sub_key_list = build_key_schedule(key_bits)
    sbox_list    = build_sboxes(key_bits)

    after_initial_permutation = permute(plaintext_bits, IP_TABLE)
    left_half  = after_initial_permutation[:32]
    right_half = after_initial_permutation[32:]

    if verbose:
        print(_DIVIDER)
        print("  IDES ENCRYPTION — Round-by-Round Trace")
        print(_DIVIDER)
        print(f"  Plaintext   : {plaintext_hex.upper()}")
        print(f"  Key         : {key_hex.upper()}")
        print(_THIN_DIV)
        print(f"  [Initial Permutation  (IP)]")
        print(f"    L0 = {bits_to_hex(left_half)}    R0 = {bits_to_hex(right_half)}")
        print(_THIN_DIV)

    for round_index in range(32):
        if verbose:
            print(f"  Round {round_index + 1:>2d}  {_ROUND_DIV[:55]}")
            print(f"    Input  :  L = {bits_to_hex(left_half)}    R = {bits_to_hex(right_half)}")

        feistel_output = f_function(
            right_half,
            sub_key_list[round_index],
            sbox_list,
            verbose=verbose,
            round_num=round_index + 1,
        )
        new_right_half = xor_bits(left_half, feistel_output)
        left_half  = right_half
        right_half = new_right_half

        if verbose:
            print(f"    Output :  L = {bits_to_hex(left_half)}    R = {bits_to_hex(right_half)}")

    pre_final_permutation = right_half + left_half
    ciphertext_bits = permute(pre_final_permutation, FP_TABLE)
    ciphertext = bits_to_hex(ciphertext_bits)

    if verbose:
        print(_THIN_DIV)
        print(f"  [Pre-FP swap]")
        print(f"    R32 + L32 = {bits_to_hex(right_half)}{bits_to_hex(left_half)}")
        print(f"  [Final Permutation (FP = IP⁻¹)]")
        print(f"    Ciphertext : {ciphertext}")
        print(_DIVIDER)

    return ciphertext


def decrypt(ciphertext_hex: str, key_hex: str, verbose: bool = False) -> str:
    """
    Decrypt a 64-bit ciphertext block using the IDES algorithm.

    Decryption is identical to encryption except the 32 sub-keys are
    applied in REVERSE order (SK32 → SK1), exploiting the symmetric
    property of Feistel networks.

    Args:
        ciphertext_hex: exactly 16 hex characters (64 bits)
        key_hex:        exactly 32 hex characters (128 bits)
        verbose:        if True, print every round's intermediate values

    Returns:
        recovered plaintext as 16 uppercase hex characters
    """
    if len(ciphertext_hex) != 16:
        raise ValueError(
            f"ciphertext_hex must be exactly 16 hex chars, got {len(ciphertext_hex)}"
        )
    if len(key_hex) != 32:
        raise ValueError(
            f"key_hex must be exactly 32 hex chars, got {len(key_hex)}"
        )

    ciphertext_bits = hex_to_bits(ciphertext_hex)
    key_bits        = hex_to_bits(key_hex)

    sub_key_list      = build_key_schedule(key_bits)
    sbox_list         = build_sboxes(key_bits)
    reversed_sub_keys = list(reversed(sub_key_list))

    after_initial_permutation = permute(ciphertext_bits, IP_TABLE)
    left_half  = after_initial_permutation[:32]
    right_half = after_initial_permutation[32:]

    if verbose:
        print(_DIVIDER)
        print("  IDES DECRYPTION — Round-by-Round Trace  (keys applied SK32→SK1)")
        print(_DIVIDER)
        print(f"  Ciphertext  : {ciphertext_hex.upper()}")
        print(f"  Key         : {key_hex.upper()}")
        print(_THIN_DIV)
        print(f"  [Initial Permutation  (IP)]")
        print(f"    L0 = {bits_to_hex(left_half)}    R0 = {bits_to_hex(right_half)}")
        print(_THIN_DIV)

    for round_index in range(32):
        orig_key_num = 32 - round_index

        if verbose:
            print(f"  Round {round_index + 1:>2d}  (SK{orig_key_num:>2d})  {_ROUND_DIV[:47]}")
            print(f"    Input  :  L = {bits_to_hex(left_half)}    R = {bits_to_hex(right_half)}")

        feistel_output = f_function(
            right_half,
            reversed_sub_keys[round_index],
            sbox_list,
            verbose=verbose,
            round_num=round_index + 1,
        )
        new_right_half = xor_bits(left_half, feistel_output)
        left_half  = right_half
        right_half = new_right_half

        if verbose:
            print(f"    Output :  L = {bits_to_hex(left_half)}    R = {bits_to_hex(right_half)}")

    pre_final_permutation = right_half + left_half
    plaintext_bits = permute(pre_final_permutation, FP_TABLE)
    plaintext = bits_to_hex(plaintext_bits)

    if verbose:
        print(_THIN_DIV)
        print(f"  [Pre-FP swap]")
        print(f"    R32 + L32 = {bits_to_hex(right_half)}{bits_to_hex(left_half)}")
        print(f"  [Final Permutation (FP = IP⁻¹)]")
        print(f"    Recovered  : {plaintext}")
        print(_DIVIDER)

    return plaintext


# ---------------------------------------------------------------------------
# Demo
# ---------------------------------------------------------------------------
if __name__ == "__main__":
    DEMO_PLAINTEXT = "0123456789ABCDEF"
    DEMO_KEY       = "AABB09182736CCDD1683F84F8C1AD287"


    print("=" * 72)
    print("  Improved DES (IDES) — Encryption & Decryption Demo")
    print("=" * 72)
    print(f"  Plaintext : {DEMO_PLAINTEXT}")
    print(f"  Key       : {DEMO_KEY}")
    print()

    # Show SHA-256 hashes and generated S-boxes for the demo key
    _print_key_material(DEMO_KEY, hex_to_bits(DEMO_KEY))

    ciphertext          = encrypt(DEMO_PLAINTEXT, DEMO_KEY, verbose=True)
    recovered_plaintext = decrypt(ciphertext,     DEMO_KEY, verbose=True)

    print(f"\n  Ciphertext (encrypted) : {ciphertext}")
    print(f"  Recovered  (decrypted) : {recovered_plaintext}")
    round_trip_result = "PASS" if recovered_plaintext.upper() == DEMO_PLAINTEXT.upper() else "FAIL"

    # ---------------------------------------------------------------------------
    # Interactive section — user chooses encrypt or decrypt
    # ---------------------------------------------------------------------------
    print()
    print("  Enter your own values (or press Enter to skip):")
    key_input = input("  Key (32 hex chars): ").strip()
    if not key_input:
        print("  Skipped.")
    else:
        if len(key_input) != 32:
            print(f"  Error: key must be exactly 32 hex chars, got {len(key_input)}")
        else:
            # Show SHA-256 and S-boxes for the user's key before operating
            try:
                key_bits_input = hex_to_bits(key_input)
                _print_key_material(key_input, key_bits_input)
            except Exception as error:
                print(f"  Error reading key: {error}")
                raise SystemExit(1)

            mode = input("  Mode — [E]ncrypt or [D]ecrypt? ").strip().upper()
            if mode not in ("E", "D", "ENCRYPT", "DECRYPT"):
                print("  Unrecognised mode. Please enter E or D.")
            else:
                is_encrypt = mode.startswith("E")
                if is_encrypt:
                    data_input = input("  Plaintext  (16 hex chars): ").strip()
                else:
                    data_input = input("  Ciphertext (16 hex chars): ").strip()

                if not data_input:
                    print("  Skipped.")
                else:
                    try:
                        if is_encrypt:
                            result = encrypt(data_input, key_input, verbose=True)
                            print(f"\n  Ciphertext : {result}")
                        else:
                            result = decrypt(data_input, key_input, verbose=True)
                            print(f"\n  Recovered  : {result}")
                    except Exception as error:
                        print(f"  Error: {error}")
