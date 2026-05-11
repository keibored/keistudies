import time
import os
import sys
import DES           # Directly importing your DES file
import improved_des  # Directly importing your IDES file

# Helper to suppress the 'print' statements inside your DES.py functions
class MutePrints:
    def __enter__(self):
        self._original_stdout = sys.stdout
        sys.stdout = open(os.devnull, 'w')
    def __exit__(self, exc_type, exc_val, exc_tb):
        sys.stdout.close()
        sys.stdout = self._original_stdout

def run_benchmark():
    ITERATIONS = 100
    pt = "123456ABCD132536"
    des_key_hex = "AABB09182736CCDD"
    ides_key_hex = "AABB09182736CCDD1683F84F8C1AD287"

    # 1. PRE-CALCULATE DES ROUND KEYS (to be fair to IDES which does this inside its function)
    # This uses the logic from your DES.py to prepare the subkeys once.
    key_bin = DES.hex2bin(des_key_hex)
    key_bin = DES.permute(key_bin, DES.keyp, 56)
    rkb, rk = [], []
    left, right = key_bin[0:28], key_bin[28:56]
    for i in range(16):
        left = DES.shift_left(left, DES.shift_table[i])
        right = DES.shift_left(right, DES.shift_table[i])
        round_key = DES.permute(left + right, DES.key_comp, 48)
        rkb.append(round_key)
        rk.append(DES.bin2hex(round_key))

    print(f"--- Benchmarking {ITERATIONS} rounds ---")

    # 2. BENCHMARK STANDARD DES
    print("Testing Standard DES...")
    start_des = time.perf_counter()
    with MutePrints():
        for _ in range(ITERATIONS):
            DES.encrypt(pt, rkb, rk)
    end_des = time.perf_counter()

    # 3. BENCHMARK IMPROVED DES (IDES)
    print("Testing Improved DES (IDES)...")
    start_ides = time.perf_counter()
    for _ in range(ITERATIONS):
        # We pass verbose=False to keep it fast
        improved_des.encrypt(pt, ides_key_hex, verbose=False)
    end_ides = time.perf_counter()

    # RESULTS
    des_time = (end_des - start_des) / ITERATIONS
    ides_time = (end_ides - start_ides) / ITERATIONS

    print("\n" + "="*40)
    print(f"DES Avg:  {des_time:.6f}s")
    print(f"IDES Avg: {ides_time:.6f}s")
    print(f"Ratio:    {ides_time/des_time:.2f}x slower")
    print("="*40)

if __name__ == "__main__":
    run_benchmark()