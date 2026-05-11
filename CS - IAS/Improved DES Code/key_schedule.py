"""
key_schedule.py — Extended key schedule for IDES.

Generates 32 × 48-bit sub-keys from a 128-bit master key using
expanded PC-1/PC-2 tables and SHA-256 mixing.
"""

from utils import permute, left_rotate, xor_bits, bits_to_bytes, sha256_hash


# PC-1: selects and permutes 56 out of 64 bits from each key half
# (drops the 8 parity bits at positions 8,16,24,32,40,48,56,64)
PC1 = [
    57, 49, 41, 33, 25, 17,  9,
     1, 58, 50, 42, 34, 26, 18,
    10,  2, 59, 51, 43, 35, 27,
    19, 11,  3, 60, 52, 44, 36,
    63, 55, 47, 39, 31, 23, 15,
     7, 62, 54, 46, 38, 30, 22,
    14,  6, 61, 53, 45, 37, 29,
    21, 13,  5, 28, 20, 12,  4,
]

# PC-2: selects 48 bits from the 56-bit C‖D state → sub-key
PC2 = [
    14, 17, 11, 24,  1,  5,
     3, 28, 15,  6, 21, 10,
    23, 19, 12,  4, 26,  8,
    16,  7, 27, 20, 13,  2,
    41, 52, 31, 37, 47, 55,
    30, 40, 51, 45, 33, 48,
    44, 49, 39, 56, 34, 53,
    46, 42, 50, 36, 29, 32,
]

# Shift schedule for 32 rounds (standard DES 16-round schedule repeated twice)
SHIFT_SCHEDULE = [
    1, 1, 2, 2, 2, 2, 2, 2, 1, 2, 2, 2, 2, 2, 2, 1,
    1, 1, 2, 2, 2, 2, 2, 2, 1, 2, 2, 2, 2, 2, 2, 1,
]


def _compute_sha256_from_key(master_key_bits: list[int]) -> bytes:
    """
    Internal helper: apply PC-1 to both key halves, concatenate,
    and return SHA-256(PC-1(key_left) || PC-1(key_right)) as raw bytes.
    """
    key_left  = master_key_bits[:64]
    key_right = master_key_bits[64:]
    key_left_permuted  = permute(key_left,  PC1)
    key_right_permuted = permute(key_right, PC1)
    combined_bytes = bits_to_bytes(key_left_permuted + key_right_permuted)
    return sha256_hash(combined_bytes)


def get_key_schedule_sha256_hex(master_key_bits: list[int]) -> str:
    """
    Return the SHA-256 intermediate hash used by the key schedule as a
    64-character uppercase hex string.  Useful for display/debugging.
    """
    return _compute_sha256_from_key(master_key_bits).hex().upper()


def build_key_schedule(master_key_bits: list[int]) -> list[list[int]]:
    """
    Generate 32 × 48-bit sub-keys from the 128-bit master key.

    Process:
    1. Split key into key_left (bits 1–64) and key_right (bits 65–128).
    2. Apply PC-1 to each half → 56 bits each.
    3. SHA-256(PC-1(key_left) ‖ PC-1(key_right)) → 256-bit intermediate key material.
    4. Split intermediate into c_register (bits 1–128) and d_register (bits 129–256).
    5. For each of 32 rounds:
       a. Left-rotate c_register and d_register by SHIFT_SCHEDULE[round_index].
       b. Take first 28 bits from each → 56-bit C‖D input.
       c. Apply PC-2 → raw 48-bit sub-key candidate.
       d. XOR with RotL(SK_{i-1}, shift_amount) to get SKi.
          (SK_0 is treated as all-zeros, so the first round is unaffected.)

    Formula: SKi = Compress(C_i ‖ D_i) ⊕ RotL(SKi−1, shift_i)
    """
    hash_bytes = _compute_sha256_from_key(master_key_bits)

    hash_bits: list[int] = []
    for byte_value in hash_bytes:
        for bit_position in range(7, -1, -1):
            hash_bits.append((byte_value >> bit_position) & 1)

    c_register = hash_bits[:128]
    d_register = hash_bits[128:]

    sub_key_list: list[list[int]] = []
    prev_sub_key: list[int] = [0] * 48  # SK_0 = all-zeros (no previous key in round 1)

    for round_index in range(32):
        shift_amount = SHIFT_SCHEDULE[round_index]
        c_register   = left_rotate(c_register, shift_amount)
        d_register   = left_rotate(d_register, shift_amount)

        cd_combined_56bits = c_register[:28] + d_register[:28]
        compressed_sub_key = permute(cd_combined_56bits, PC2)

        # XOR compression step: SKi = Compress(C_i ‖ D_i) ⊕ RotL(SK_{i-1}, shift_i)
        rotated_prev = left_rotate(prev_sub_key, shift_amount)
        sub_key      = xor_bits(compressed_sub_key, rotated_prev)

        sub_key_list.append(sub_key)
        prev_sub_key = sub_key

    return sub_key_list
