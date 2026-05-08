import java.io.Console;
import java.text.NumberFormat;
import java.util.ArrayList;
import java.util.Optional;
import java.util.Scanner;

interface EmployeeActions {
    default void showHeader(String action) {
        System.out.println("\n======== " + action + " ========");
    }

    default void onComputeSalary() {
        System.out.println("Computing Salary...");
    }

    default void onEmployeeAdded() {
        System.out.println("Employee Added Successfully :)");
    }
}

abstract class Employee {
    protected int id;
    protected String name;
    protected String department;

    public Employee(int id, String name, String department) {
        this.id = id;
        this.name = name;
        this.department = department;
    }

    public int getId() {
        return id;
    }

    public void update(String name, String department) {
        if (!name.isBlank()) this.name = name;
        if (!department.isBlank()) this.department = department;
    }

    public abstract double computeSalary();
    public abstract String getRole();

    @Override
    public String toString() {
        NumberFormat nf = NumberFormat.getNumberInstance();
        return String.format(
            "\n~ ID: %d\n~ Name: %s\n~ Employee Role: %s\n~ Dept: %s\n~ Salary: PHP %s",
            id, name, getRole(), department, nf.format(computeSalary())
        );
    }
}

class Manager extends Employee {
    public Manager(int id, String name, String department) {
        super(id, name, department);
    }

    @Override
    public double computeSalary() {
        return 8 * 20 * 500;
    }

    @Override
    public String getRole() {
        return "Manager";
    }
}

class Assistant extends Employee {
    public Assistant(int id, String name, String department) {
        super(id, name, department);
    }

    @Override
    public double computeSalary() {
        return 9 * 24 * 300;
    }

    @Override
    public String getRole() {
        return "Assistant";
    }
}

class Developer extends Employee {
    public Developer(int id, String name, String department) {
        super(id, name, department);
    }

    @Override
    public double computeSalary() {
        return 10 * 24 * 100;
    }

    @Override
    public String getRole() {
        return "Developer";
    }
}

public class EMS implements EmployeeActions {
    static ArrayList<Employee> employees = new ArrayList<>();
    static Scanner scanner = new Scanner(System.in);
    static EMS instance = new EMS();

    public static void main(String[] args) {
        if (!login()) return;

        while (true) {
            System.out.print("""
            ========= EMPLOYEE MANAGEMENT SYSTEM ========
            1. Add Employee
            2. Display Employee by ID
            3. Remove Employee
            4. Update Employee
            5. Display All Employees
            6. Exit
            Enter choice here: 
            """);

            String input = scanner.nextLine();

            switch (input) {
                case "1" -> addEmployee();
                case "2" -> displayEmployee();
                case "3" -> removeEmployee();
                case "4" -> updateEmployee();
                case "5" -> displayAllEmployees();
                case "6" -> {
                    System.out.println("Goodbye!");
                    return;
                }
                default -> System.out.println("Invalid choice. Please try again.");
            }
        }
    }

    static boolean login() {
        final String USERNAME = "Admin";
        final String PASSWORD = "1234";
        int attempts = 3;

        while (attempts > 0) {
            System.out.println("\n===== EMPLOYEE MANAGEMENT SYSTEM LOGIN ======");
            System.out.print("Username: ");
            String username = scanner.nextLine();

            String password;
            Console console = System.console();

            if (console != null) {
                char[] pwdArray = console.readPassword("Password: ");
                password = new String(pwdArray);
            } else {
                System.out.print("Password: ");
                password = scanner.nextLine();
            }

            if (USERNAME.equals(username) && PASSWORD.equals(password)) {
                System.out.println("Login successful!\n");
                return true;
            }

            attempts--;
            System.out.println("Invalid credentials. Attempts left: " + attempts);
        }

        System.out.println("Too many failed attempts. Exiting program...");
        return false;
    }

    static void addEmployee() {
        try {
            instance.showHeader("Add Employee");

            System.out.print("ID: ");
            int id = Integer.parseInt(scanner.nextLine().trim());

            if (employees.stream().anyMatch(e -> e.getId() == id)) {
                System.out.println("ID already exists.");
                return;
            }

            System.out.print("Name: ");
            String name = scanner.nextLine().trim();

            System.out.print("Department: ");
            String dept = scanner.nextLine().trim();

            if (name.isBlank() || dept.isBlank()) {
                System.out.println("Name and department cannot be blank.");
                return;
            }

            System.out.print("Employee Role (1=Manager, 2=Assistant, 3=Developer): ");
            String role = scanner.nextLine().trim();

            Employee newEmployee = switch (role) {
                case "1" -> new Manager(id, name, dept);
                case "2" -> new Assistant(id, name, dept);
                case "3" -> new Developer(id, name, dept);
                default -> null;
            };

            if (newEmployee == null) {
                System.out.println("Invalid role.");
                return;
            }

            instance.onComputeSalary();
            employees.add(newEmployee);
            instance.onEmployeeAdded();

        } catch (Exception e) {
            System.out.println("Invalid input.");
        }
    }

    static void displayEmployee() {
        try {
            System.out.print("Enter ID to show: ");
            int id = Integer.parseInt(scanner.nextLine().trim());

            Optional<Employee> employee = employees.stream()
                    .filter(e -> e.getId() == id)
                    .findFirst();

            if (employee.isPresent()) {
                System.out.println(employee.get());
            } else {
                System.out.println("Employee not found.");
            }

        } catch (Exception e) {
            System.out.println("Invalid input.");
        }
    }

    static void removeEmployee() {
        try {
            System.out.print("Enter ID to remove: ");
            int id = Integer.parseInt(scanner.nextLine().trim());

            if (employees.removeIf(e -> e.getId() == id)) {
                System.out.println("Employee removed.");
            } else {
                System.out.println("Employee not found.");
            }

        } catch (Exception e) {
            System.out.println("Invalid ID.");
        }
    }

    static void updateEmployee() {
        try {
            System.out.print("Enter ID to update: ");
            int id = Integer.parseInt(scanner.nextLine().trim());

            Optional<Employee> optional = employees.stream()
                    .filter(e -> e.getId() == id)
                    .findFirst();

            if (optional.isEmpty()) {
                System.out.println("Employee not found.");
                return;
            }

            Employee oldEmployee = optional.get();

            System.out.print("New name [" + oldEmployee.name + "]: ");
            String newName = scanner.nextLine().trim();
            if (newName.isBlank()) newName = oldEmployee.name;

            System.out.print("New department [" + oldEmployee.department + "]: ");
            String newDept = scanner.nextLine().trim();
            if (newDept.isBlank()) newDept = oldEmployee.department;

            System.out.print("New role (1=Manager, 2=Assistant, 3=Developer, Enter=same): ");
            String newRole = scanner.nextLine().trim();

            Employee updatedEmployee = switch (newRole) {
                case "1" -> new Manager(id, newName, newDept);
                case "2" -> new Assistant(id, newName, newDept);
                case "3" -> new Developer(id, newName, newDept);
                case "" -> oldEmployee;
                default -> null;
            };

            if (updatedEmployee == null) {
                System.out.println("Invalid role.");
                return;
            }

            updatedEmployee.update(newName, newDept);
            employees.remove(oldEmployee);
            employees.add(updatedEmployee);

            System.out.println("Employee updated.");

        } catch (Exception e) {
            System.out.println("Error updating employee.");
        }
    }

    static void displayAllEmployees() {
        if (employees.isEmpty()) {
            System.out.println("No employees found.");
            return;
        }

        for (Employee e : employees) {
            System.out.println(e);
            System.out.println("---------------------------------------------");
        }
    }
}