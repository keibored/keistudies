"""
sbox_generator.py — Dynamic S-box generation for IDES.

Generates 8 key-dependent 4×16 S-boxes using SHA-256 seeding and
Fisher-Yates permutation, with Walsh-Hadamard non-linearity verification.
"""

from utils import bits_to_bytes, sha256_hash

# Minimum non-linearity threshold for each row of a 4→4 S-box.
# For a 4-to-4 bijection, max possible NL = 4; DES S-boxes achieve ~4.
# We require NL ≥ 2 to ensure meaningful non-linearity.
NL_THRESHOLD = 2


def fisher_yates_shuffle(seed_bytes: bytes) -> list[int]:
    """
    Shuffle [0..15] using Fisher-Yates with the given seed bytes.
    Uses 2 seed bytes per swap step (high_byte*256 + low_byte) for lower modulo bias.
    Seed bytes are consumed sequentially with wrap-around.
    """
    shuffled_values = list(range(16))
    seed_byte_index = 0
    for swap_index in range(15, 0, -1):
        high_byte = seed_bytes[seed_byte_index       % len(seed_bytes)]
        low_byte  = seed_bytes[(seed_byte_index + 1) % len(seed_bytes)]
        seed_byte_index += 2
        swap_target = (high_byte * 256 + low_byte) % (swap_index + 1)
        shuffled_values[swap_index], shuffled_values[swap_target] = (
            shuffled_values[swap_target], shuffled_values[swap_index]
        )
    return shuffled_values


def _wht(boolean_function: list[int]) -> list[int]:
    """
    Compute the Walsh-Hadamard Transform of a Boolean function defined
    on {0..n-1} where n = len(boolean_function) must be a power of 2.

    W_f(a) = Σ_{x=0}^{n-1} (-1)^{ f(x) ⊕ <a,x> }
    where <a,x> = popcount(a & x) mod 2.

    Returns the full WHT spectrum as a list of integers.
    """
    input_size = len(boolean_function)
    # Convert from {0,1} to {+1,-1}: 0 → +1, 1 → −1
    wht_spectrum = [1 - 2 * bit_value for bit_value in boolean_function]
    step_size = 1
    while step_size < input_size:
        for block_start in range(0, input_size, step_size * 2):
            for offset in range(block_start, block_start + step_size):
                left_val  = wht_spectrum[offset]
                right_val = wht_spectrum[offset + step_size]
                wht_spectrum[offset]            = left_val + right_val
                wht_spectrum[offset + step_size] = left_val - right_val
        step_size *= 2
    return wht_spectrum


def _row_nonlinearity(sbox_row: list[int]) -> int:
    """
    Compute the non-linearity of a single S-box row (a 4→4 bijection).

    For each of the 4 output bits, treat output_bit_function(x) = (row[x] >> bit_index) & 1
    as a Boolean function on 4-bit inputs. Non-linearity of that function is:
        NL = 2^3 - (1/2) * max_a |W(a)|

    The row non-linearity is the minimum NL across all 4 output bits.
    Index a=0 is skipped to ignore the constant (trivial) term.
    """
    min_nonlinearity = 8   # upper bound for a 4-input Boolean function
    for output_bit_index in range(4):
        output_bit_function = [(sbox_row[input_val] >> output_bit_index) & 1
                               for input_val in range(16)]
        wht_spectrum       = _wht(output_bit_function)
        max_walsh_coeff    = max(abs(wht_spectrum[walsh_index])
                                for walsh_index in range(1, 16))
        nonlinearity       = 8 - max_walsh_coeff // 2
        if nonlinearity < min_nonlinearity:
            min_nonlinearity = nonlinearity
    return min_nonlinearity


def _row_passes(sbox_row: list[int]) -> bool:
    """Return True if the row meets the NL_THRESHOLD."""
    return _row_nonlinearity(sbox_row) >= NL_THRESHOLD


def build_sboxes(master_key_bits: list[int]) -> list[list[list[int]]]:
    """
    Generate 8 dynamic S-boxes (each 4×16) from the 128-bit master key.

    Process:
    1. Compute master_seed = SHA-256(128-bit key bytes).
    2. For each S-box (sbox_index 0–7) and each row (row_index 0–3):
       a. Derive row_hash = SHA-256(master_seed ‖ sbox_index_byte ‖ row_index_byte ‖ attempt_byte).
       b. Run Fisher-Yates on [0..15] using row_hash.
       c. Verify row non-linearity ≥ NL_THRESHOLD via Walsh-Hadamard.
       d. If the row fails, increment generation_attempt and retry with a modified seed.

    Returns: list of 8 S-boxes, each a list of 4 rows × 16 columns.
    """
    key_bytes   = bits_to_bytes(master_key_bits)
    master_seed = sha256_hash(key_bytes)

    sbox_list: list[list[list[int]]] = []
    for sbox_index in range(8):
        current_sbox: list[list[int]] = []
        for row_index in range(4):
            generation_attempt = 0
            while True:
                row_seed_input = master_seed + bytes([sbox_index, row_index, generation_attempt])
                row_hash       = sha256_hash(row_seed_input)
                candidate_row  = fisher_yates_shuffle(row_hash)
                if _row_passes(candidate_row):
                    current_sbox.append(candidate_row)
                    break
                generation_attempt += 1
        sbox_list.append(current_sbox)

    return sbox_list


def get_sbox_master_seed_hex(master_key_bits: list[int]) -> str:
    """
    Return the SHA-256 master seed used to derive all S-boxes as a
    64-character uppercase hex string.  Useful for display/debugging.
    """
    key_bytes   = bits_to_bytes(master_key_bits)
    master_seed = sha256_hash(key_bytes)
    return master_seed.hex().upper()


def display_sboxes(sbox_list: list[list[list[int]]]) -> None:
    """
    Pretty-print all 8 S-boxes in a 4×16 grid, showing each value as
    a single hex digit (0–F).  Also prints the non-linearity score for
    every row so you can verify the threshold was met.
    """
    col_header = "     " + "  ".join(f"{c:X}" for c in range(16))
    divider    = "  " + "-" * (len(col_header) - 2)

    for sbox_index, sbox in enumerate(sbox_list):
        print(f"  S-Box {sbox_index + 1}:")
        print(col_header)
        print(divider)
        for row_index, row in enumerate(sbox):
            values_str = "  ".join(f"{v:X}" for v in row)
            nl = _row_nonlinearity(row)
            print(f"  {row_index}  | {values_str}   (NL={nl})")
        print()


def get_row_nonlinearity_report(sbox_list: list[list[list[int]]]) -> list[list[int]]:
    """
    Return a 8×4 matrix of non-linearity values for all S-box rows.
    Useful for testing and reporting.
    """
    return [
        [_row_nonlinearity(sbox_list[sbox_index][row_index]) for row_index in range(4)]
        for sbox_index in range(8)
    ]
