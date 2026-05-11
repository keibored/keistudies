"""
feistel.py — Feistel F-function for IDES.
"""

from utils import permute, xor_bits, bits_to_hex


# E Expansion: expands the 32-bit right half to 48 bits
E_TABLE = [
    32,  1,  2,  3,  4,  5,
     4,  5,  6,  7,  8,  9,
     8,  9, 10, 11, 12, 13,
    12, 13, 14, 15, 16, 17,
    16, 17, 18, 19, 20, 21,
    20, 21, 22, 23, 24, 25,
    24, 25, 26, 27, 28, 29,
    28, 29, 30, 31, 32,  1,
]

# P Permutation: permutes the 32-bit S-box output
P_TABLE = [
    16,  7, 20, 21,
    29, 12, 28, 17,
     1, 15, 23, 26,
     5, 18, 31, 10,
     2,  8, 24, 14,
    32, 27,  3,  9,
    19, 13, 30,  6,
    22, 11,  4, 25,
]


def _bits48_to_hex(bits: list[int]) -> str:
    """Display 48-bit list as 12 hex chars (pad to 48 bits)."""
    padded = bits + [0] * (48 - len(bits))
    result = ""
    for i in range(0, 48, 4):
        nibble = (padded[i] << 3) | (padded[i+1] << 2) | (padded[i+2] << 1) | padded[i+3]
        result += format(nibble, 'X')
    return result


def f_function(
    right_half: list[int],
    sub_key: list[int],
    sbox_list: list[list[list[int]]],
    verbose: bool = False,
    round_num: int = 0,
) -> list[int]:
    """
    The Feistel F-function:
    1. Expand right_half from 32 bits → 48 bits using E_TABLE.
    2. XOR expanded right_half with the 48-bit sub_key.
    3. Split result into 8 groups of 6 bits.
    4. For each group use the dynamic S-box: 6 bits → 4 bits.
       - sbox_row = first bit (MSB) and last bit (LSB) of the 6-bit group.
       - sbox_col = middle 4 bits.
    5. Concatenate 8 × 4-bit outputs → 32 bits.
    6. Permute 32 bits using P_TABLE.
    """
    expanded_right  = permute(right_half, E_TABLE)
    xored_with_key  = xor_bits(expanded_right, sub_key)

    substituted_bits: list[int] = []
    for sbox_index in range(8):
        six_bit_group = xored_with_key[sbox_index * 6 : (sbox_index + 1) * 6]
        sbox_row = (six_bit_group[0] << 1) | six_bit_group[5]
        sbox_col = ((six_bit_group[1] << 3) | (six_bit_group[2] << 2)
                    | (six_bit_group[3] << 1) |  six_bit_group[4])
        sbox_value = sbox_list[sbox_index][sbox_row][sbox_col]
        substituted_bits.extend([
            (sbox_value >> 3) & 1,
            (sbox_value >> 2) & 1,
            (sbox_value >> 1) & 1,
             sbox_value       & 1,
        ])

    permuted_output = permute(substituted_bits, P_TABLE)

    if verbose:
        indent = "    "
        print(f"{indent}  E-Expansion  (48-bit) : {_bits48_to_hex(expanded_right)}")
        print(f"{indent}  Sub-Key      (48-bit) : {_bits48_to_hex(sub_key)}")
        print(f"{indent}  XOR w/ Key   (48-bit) : {_bits48_to_hex(xored_with_key)}")
        print(f"{indent}  After S-Box  (32-bit) : {bits_to_hex(substituted_bits)}")
        print(f"{indent}  After P-Perm (32-bit) : {bits_to_hex(permuted_output)}")

    return permuted_output