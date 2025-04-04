<?php
// Database configuration
$servername = "localhost";
$username = "root"; // default XAMPP username
$password = ""; // default XAMPP password
$dbname = "payrollDb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $firstname = $conn->real_escape_string($_POST['firstname']);
    $lastname = $conn->real_escape_string($_POST['lastname']);
    $othername = $conn->real_escape_string($_POST['othername']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash password
    $gender = $conn->real_escape_string($_POST['gender']);
    $basic_pay = floatval($_POST['basic_pay']);
    
    // Combine names for display
    $fullname = $firstname . ' ' . ($othername ? $othername . ' ' : '') . $lastname;
    
    // Allowances calculations
    $Housingallowance = 0.8 * $basic_pay;
    $Transportallowance = 0.5 * $basic_pay;
    $Medicalallowance = 0.6 * $basic_pay;
    $Overtime = 10 * 30000;
    
    // Gross Pay
    $Gross_pay = $basic_pay + $Overtime + $Housingallowance + $Transportallowance + $Medicalallowance;
    
    // NSSF (15% of Gross Pay)
    $NSSF = 0.15 * $Gross_pay;
    
    // PAYE Calculation
    if ($Gross_pay < 200000) {
        $PAYE = 0;
    } elseif ($Gross_pay >= 200000 && $Gross_pay < 350000) {
        $PAYE = 0.10 * ($Gross_pay - 200000);
    } elseif ($Gross_pay >= 350000 && $Gross_pay < 500000) {
        $PAYE = 20000 + (0.20 * ($Gross_pay - 350000));
    } else {
        $PAYE = 60500 + (0.30 * ($Gross_pay - 500000));
    }
    
    // Total Deductions
    $Total_deductions = $NSSF + $PAYE;
    
    // Net Pay
    $Net_pay = $Gross_pay - $Total_deductions;
    
    // SQL to insert employee data
    $sql = "INSERT INTO employees (firstname, lastname, othername, email, password, gender, basic_pay, 
            housing_allowance, transport_allowance, medical_allowance, overtime, gross_pay, 
            nssf, paye, total_deductions, net_pay)
            VALUES ('$firstname', '$lastname', '$othername', '$email', '$password', '$gender', $basic_pay,
            $Housingallowance, $Transportallowance, $Medicalallowance, $Overtime, $Gross_pay,
            $NSSF, $PAYE, $Total_deductions, $Net_pay)";
    
    if ($conn->query($sql) === TRUE) {
        $success_message = "Employee record created successfully";
    } else {
        $error_message = "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mukwano Industries Payroll System</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <h1>Mukwano Industries Payroll System</h1>
    
    <?php if (isset($success_message)): ?>
        <div class="success"><?php echo $success_message; ?></div>
    <?php endif; ?>
    
    <?php if (isset($error_message)): ?>
        <div class="error"><?php echo $error_message; ?></div>
    <?php endif; ?>
    
    <form method="post">
        <div class="one">
            <input type="text" id="firstname" name="firstname" placeholder="First Name" required><br>
            <input type="text" id="lastname" name="lastname" placeholder="Last Name" required><br>
            <input type="text" id="othername" name="othername" placeholder="Other Name"><br>
            <input type="email" id="email" name="email" placeholder="Email" required><br>
            <input type="password" id="password" name="password" placeholder="Password" required><br>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required><br>
        
            <label>Gender:</label>
            <input type="radio" id="male" name="gender" value="male" required>
            <label for="male">Male</label>
            <input type="radio" id="female" name="gender" value="female">
            <label for="female">Female</label><br><br>
        
            <input type="number" name="basic_pay" placeholder="Basic Pay" id="basic_pay" required>
            <button type="submit">Calculate & Save</button>
        </div>
    </form>
    
    <?php if (isset($Net_pay)): ?>
        <h3>Payroll Results for <?php echo htmlspecialchars($fullname); ?></h3>
        <p>Basic Pay: <?php echo number_format($basic_pay, 2); ?></p>
        <p>Housing Allowance: <?php echo number_format($Housingallowance, 2); ?></p>
        <p>Transport Allowance: <?php echo number_format($Transportallowance, 2); ?></p>
        <p>Medical Allowance: <?php echo number_format($Medicalallowance, 2); ?></p>
        <p>Overtime: <?php echo number_format($Overtime, 2); ?></p>
        <p>Gross Pay: <?php echo number_format($Gross_pay, 2); ?></p>
        <p>NSSF (15%): <?php echo number_format($NSSF, 2); ?></p>
        <p>PAYE: <?php echo number_format($PAYE, 2); ?></p>
        <p>Total Deductions: <?php echo number_format($Total_deductions, 2); ?></p>
        <p><strong>Net Pay: <?php echo number_format($Net_pay, 2); ?></strong></p>
    <?php endif; ?>
    
    <?php $conn->close(); ?>
</body>
</html>