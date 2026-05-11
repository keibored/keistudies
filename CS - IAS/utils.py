"""
utils.py — Bit manipulation helpers for IDES.
"""

import hashlib


def hex_to_bits(hex_string: str) -> list[int]:
    """Convert a hex string to a flat list of bits (MSB first)."""
    bit_list = []
    for hex_char in hex_string.upper():
        nibble_value = int(hex_char, 16)
        for bit_position in range(3, -1, -1):
            bit_list.append((nibble_value >> bit_position) & 1)
    return bit_list


def bits_to_hex(bit_list: list[int]) -> str:
    """Convert a flat list of bits (MSB first) to a hex string."""
    hex_string = ""
    for start_index in range(0, len(bit_list), 4):
        nibble_bits = bit_list[start_index : start_index + 4]
        nibble_value = (nibble_bits[0] * 8 + nibble_bits[1] * 4
                        + nibble_bits[2] * 2 + nibble_bits[3])
        hex_string += format(nibble_value, 'x').upper()
    return hex_string


def bits_to_bytes(bit_list: list[int]) -> bytes:
    """Pack a list of bits (MSB first) into a bytes object."""
    byte_array = bytearray(len(bit_list) // 8)
    for bit_index, bit_value in enumerate(bit_list):
        if bit_value:
            byte_array[bit_index // 8] |= (1 << (7 - (bit_index % 8)))
    return bytes(byte_array)


def permute(bit_list: list[int], permutation_table: list[int]) -> list[int]:
    """Apply a permutation table (1-indexed positions) to a bit list."""
    return [bit_list[position - 1] for position in permutation_table]


def left_rotate(bit_list: list[int], num_positions: int) -> list[int]:
    """Left circular rotate a bit list by num_positions positions."""
    num_positions = num_positions % len(bit_list)
    return bit_list[num_positions:] + bit_list[:num_positions]


def xor_bits(bit_list_a: list[int], bit_list_b: list[int]) -> list[int]:
    """XOR two equal-length bit lists element-wise."""
    return [bit_a ^ bit_b for bit_a, bit_b in zip(bit_list_a, bit_list_b)]


def sha256_hash(data_bytes: bytes) -> bytes:
    """Compute SHA-256 hash of the given bytes and return the digest."""
    return hashlib.sha256(data_bytes).digest()
