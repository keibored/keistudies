from collections import deque, defaultdict
import matplotlib.pyplot as plt
import copy
import os
from colorama import init, Fore

init(autoreset=True)

# ================= UI COLORS =================
BOX = Fore.MAGENTA
HEADER = Fore.LIGHTMAGENTA_EX
TEXT = Fore.MAGENTA
INPUT = Fore.LIGHTMAGENTA_EX
ERROR = Fore.RED
WIDTH = 70

# ================= UI =================
def line():
    print(BOX + "+" + "-" * WIDTH + "+")

def center(text):
    print(BOX + "|" + HEADER + text.center(WIDTH) + BOX + "|")

def section(title):
    line()
    center(title)
    line()

def clear():
    os.system('cls' if os.name == 'nt' else 'clear')

# ================= INPUT =================
def get_int(prompt, min_val=None):
    while True:
        try:
            val = int(input(INPUT + prompt))

            if val == -1:
                return -1

            if min_val is not None and val < min_val:
                print(ERROR + f"Value must be >= {min_val}")
                continue

            return val

        except:
            print(ERROR + "Invalid input.")

# ================= PROCESS =================
class Process:
    def __init__(self, pid, at, bt, pr=0):
        self.pid = pid
        self.at = at
        self.bt = bt
        self.rt = bt
        self.pr = pr
        self.ct = 0

# ================= GANTT =================
def add_block(g, pid, start, end):
    if g and g[-1][0] == pid:
        g[-1] = (pid, g[-1][1], end)
    else:
        g.append((pid, start, end))

def add_idle(g, t, next_t):
    add_block(g, "IDLE", t, next_t)

# ================= COLORS =================
def get_color_map(processes):
    pastel_colors = [
        "#FFADAD", "#FFD6A5", "#FDFFB6",
        "#CAFFBF", "#9BF6FF", "#A0C4FF",
        "#BDB2FF", "#FFC6FF", "#D0F4DE",
        "#FDE2E4", "#E4C1F9", "#F1F0C0"
    ]

    color_map = {}

    for i, p in enumerate(processes):
        color_map[p.pid] = pastel_colors[i % len(pastel_colors)]

    color_map["IDLE"] = "#C0C0C0"

    return color_map

# ================= INPUT PROCESSES =================
def input_processes():
    section("INPUT PROCESSES")

    print(TEXT + "Press -1 to clear all the processes\n")

    n = get_int("Enter number of processes (min 3): ", 3)

    if n == -1:
        return None

    ps = []

    for i in range(n):
        print(TEXT + f"\nProcess P{i + 1}")

        at = get_int("  Arrival Time  : ", 0)
        if at == -1:
            return None

        bt = get_int("  Burst Time    : ", 1)
        if bt == -1:
            return None

        pr = get_int("  Priority Value (0 if unused): ", 0)
        if pr == -1:
            return None

        ps.append(Process(f"P{i + 1}", at, bt, pr))

    return ps

# ================= PRIORITY RULE =================
def get_priority_type():
    section("PRIORITY VALUE RULE")

    print(TEXT + "Choose how Priority Values should be ranked:")
    print(TEXT + "1. Smallest Priority Value = Higher Priority")
    print(TEXT + "2. Largest Priority Value = Higher Priority")

    while True:
        choice = get_int("Choose Priority Value rule: ", 1)

        if choice in [1, 2]:
            return choice

        print(ERROR + "Please choose 1 or 2 only.")

def get_priority_rule_text(priority_type):
    if priority_type == 1:
        return "Smallest Priority Value = Higher Priority"
    return "Largest Priority Value = Higher Priority"

def is_higher_priority(new_pr, current_pr, priority_type):
    if priority_type == 1:
        return new_pr < current_pr
    return new_pr > current_pr

# ================= FCFS =================
def fcfs(ps):
    ps.sort(key=lambda x: (x.at, x.pid))

    t = 0
    g = []

    for p in ps:
        if t < p.at:
            add_idle(g, t, p.at)
            t = p.at

        start = t
        t += p.bt
        p.ct = t

        add_block(g, p.pid, start, t)

    return g

# ================= SJF NON-PREEMPTIVE =================
def sjf(ps):
    ps.sort(key=lambda x: (x.at, x.pid))

    t = 0
    done = []
    g = []

    while len(done) < len(ps):
        ready = [p for p in ps if p.at <= t and p not in done]

        if not ready:
            next_at = min(p.at for p in ps if p not in done)
            add_idle(g, t, next_at)
            t = next_at
            continue

        p = min(ready, key=lambda x: (x.bt, x.at, x.pid))

        start = t
        t += p.bt
        p.ct = t

        add_block(g, p.pid, start, t)
        done.append(p)

    return g

# ================= SRT / SRTF =================
def srt(ps):
    ps.sort(key=lambda x: (x.at, x.pid))

    t = 0
    g = []

    while any(p.rt > 0 for p in ps):
        ready = [p for p in ps if p.at <= t and p.rt > 0]

        if not ready:
            next_at = min(p.at for p in ps if p.rt > 0)
            add_idle(g, t, next_at)
            t = next_at
            continue

        p = min(ready, key=lambda x: (x.rt, x.at, x.pid))

        start = t
        t += 1
        p.rt -= 1

        add_block(g, p.pid, start, t)

        if p.rt == 0:
            p.ct = t

    return g

# ================= ROUND ROBIN =================
def rr(ps, q):
    ps.sort(key=lambda x: (x.at, x.pid))

    rq = deque()
    t = 0
    i = 0
    g = []

    while rq or i < len(ps):
        while i < len(ps) and ps[i].at <= t:
            rq.append(ps[i])
            i += 1

        if not rq:
            next_at = ps[i].at
            add_idle(g, t, next_at)
            t = next_at
            continue

        p = rq.popleft()

        start = t
        run = min(q, p.rt)

        t += run
        p.rt -= run

        add_block(g, p.pid, start, t)

        while i < len(ps) and ps[i].at <= t:
            rq.append(ps[i])
            i += 1

        if p.rt > 0:
            rq.append(p)
        else:
            p.ct = t

    return g

# ================= PRIORITY =================
def priority(ps, preempt=False, priority_type=1):
    ps.sort(key=lambda x: (x.at, x.pid))

    t = 0
    g = []

    while any(p.rt > 0 for p in ps):
        ready = [p for p in ps if p.at <= t and p.rt > 0]

        if not ready:
            next_at = min(p.at for p in ps if p.rt > 0)
            add_idle(g, t, next_at)
            t = next_at
            continue

        if priority_type == 1:
            p = min(ready, key=lambda x: (x.pr, x.at, x.pid))
        else:
            p = sorted(ready, key=lambda x: (-x.pr, x.at, x.pid))[0]

        if not preempt:
            start = t
            t += p.rt
            p.rt = 0
            p.ct = t

            add_block(g, p.pid, start, t)

        else:
            start = t
            t += 1
            p.rt -= 1

            add_block(g, p.pid, start, t)

            if p.rt == 0:
                p.ct = t

    return g

# ================= PRIORITY + ROUND ROBIN =================
def priority_rr(ps, q, priority_type=1):
    ps.sort(key=lambda x: (x.at, x.pid))

    queues = defaultdict(deque)
    t = 0
    i = 0
    g = []
    completed = 0
    n = len(ps)

    while completed < n:
        while i < n and ps[i].at <= t:
            queues[ps[i].pr].append(ps[i])
            i += 1

        active_priorities = [pr for pr in queues if queues[pr]]

        if not active_priorities:
            if i < n:
                next_at = ps[i].at
                add_idle(g, t, next_at)
                t = next_at
                continue

        if priority_type == 1:
            selected_priority = min(active_priorities)
        else:
            selected_priority = max(active_priorities)

        p = queues[selected_priority].popleft()

        run_time = 0

        while run_time < q and p.rt > 0:
            start = t
            t += 1
            run_time += 1
            p.rt -= 1

            add_block(g, p.pid, start, t)

            higher_priority_arrived = False

            while i < n and ps[i].at <= t:
                queues[ps[i].pr].append(ps[i])

                if is_higher_priority(ps[i].pr, p.pr, priority_type):
                    higher_priority_arrived = True

                i += 1

            if higher_priority_arrived:
                break

        if p.rt > 0:
            queues[p.pr].append(p)
        else:
            p.ct = t
            completed += 1

    return g

# ================= GRAPH =================
def draw_graph(g, name, color_map):
    fig, ax = plt.subplots(figsize=(10, 3))

    y = 0
    times = set()

    for pid, start, end in g:
        ax.barh(
            y,
            end - start,
            left=start,
            color=color_map.get(pid),
            edgecolor='black',
            linewidth=1
        )

        ax.text(
            (start + end) / 2,
            y,
            pid,
            ha='center',
            va='center',
            color='black',
            fontweight='bold'
        )

        times.add(start)
        times.add(end)

    times = sorted(times)

    ax.set_xticks(times)
    ax.set_xlim(min(times), max(times))
    ax.set_title(f"Gantt Chart - {name}")
    ax.set_yticks([])
    ax.grid(axis='x', linestyle='--')

    plt.tight_layout()
    plt.show()

# ================= METRICS =================
def metrics(ps):
    section("PROCESS TABLE")

    col = 10
    table_width = col * 7

    print(
        HEADER +
        f"{'PID':<{col}}"
        f"{'AT':<{col}}"
        f"{'BT':<{col}}"
        f"{'PV':<{col}}"
        f"{'WT':<{col}}"
        f"{'TAT':<{col}}"
        f"{'CT':<{col}}"
    )

    print(BOX + "-" * table_width)

    total_wt = 0
    total_tat = 0

    for p in ps:
        tat = p.ct - p.at
        wt = tat - p.bt

        total_wt += wt
        total_tat += tat

        print(
            TEXT +
            f"{p.pid:<{col}}"
            f"{p.at:<{col}}"
            f"{p.bt:<{col}}"
            f"{p.pr:<{col}}"
            f"{wt:<{col}}"
            f"{tat:<{col}}"
            f"{p.ct:<{col}}"
        )

    print(BOX + "-" * table_width)

    print(
        TEXT +
        f"{'AVG':<{col}}"
        f"{'':<{col}}"
        f"{'':<{col}}"
        f"{'':<{col}}"
        f"{total_wt / len(ps):<{col}.2f}"
        f"{total_tat / len(ps):<{col}.2f}"
    )

# ================= MAIN =================
if __name__ == "__main__":
    while True:
        clear()

        section("CPU SCHEDULING SIMULATOR")

        processes = input_processes()

        if processes is None:
            continue

        while True:
            section("ALGORITHM MENU")

            print(TEXT + "1. FCFS")
            print(TEXT + "2. SJF")
            print(TEXT + "3. SRT")
            print(TEXT + "4. Round Robin")
            print(TEXT + "5. Priority (Non-Preemptive)")
            print(TEXT + "6. Priority (Preemptive)")
            print(TEXT + "7. Priority + Round Robin")
            print(TEXT + "0. Exit Program")

            choice = get_int("Select: ")

            if choice == 0:
                exit()

            ps = copy.deepcopy(processes)
            color_map = get_color_map(ps)

            if choice == 1:
                g = fcfs(ps)
                name = "FCFS"

            elif choice == 2:
                g = sjf(ps)
                name = "SJF"

            elif choice == 3:
                g = srt(ps)
                name = "SRT"

            elif choice == 4:
                q = get_int("Quantum: ", 1)
                g = rr(ps, q)
                name = f"Round Robin | Quantum = {q}"

            elif choice == 5:
                priority_type = get_priority_type()
                rule = get_priority_rule_text(priority_type)
                g = priority(ps, False, priority_type)
                name = f"Priority (Non-Preemptive) | {rule}"

            elif choice == 6:
                priority_type = get_priority_type()
                rule = get_priority_rule_text(priority_type)
                g = priority(ps, True, priority_type)
                name = f"Priority (Preemptive) | {rule}"

            elif choice == 7:
                priority_type = get_priority_type()
                rule = get_priority_rule_text(priority_type)
                q = get_int("Quantum: ", 1)
                g = priority_rr(ps, q, priority_type)
                name = f"Priority + Round Robin | {rule} | Quantum = {q}"

            else:
                continue

            metrics(ps)

            input(HEADER + "\nPress Enter for Gantt Chart...")

            draw_graph(g, name, color_map)

            again = get_int(
                "\n1. Run Again\n2. New Processes\n3. Exit\nChoice: "
            )

            if again == 1:
                continue
            elif again == 2:
                break
            else:
                exit()