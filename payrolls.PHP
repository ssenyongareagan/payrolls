<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $basic_pay = floatval($_POST['basic_pay']);
    
    // Allowances
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
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mukwano Industries Payroll System</title>
    <link rel="stylesheet" type="text/css" href="styles22.css">
</head>
<body>
    <h1>Mukwano Industries Payroll System</h1>
    <form method="post">

    <div class="one">
            <input type="text" id="firstname" name="firstname" placeholder="First Name" required><br>
            <input type="text" id="lastname" name="lastname" placeholder="Last Name" required><br>
            <input type="text" id="othername" name="othername" placeholder="Other Name"><br>
            <input type="email" id="email" name="email" placeholder="Email" required><br>
            <input type="password" id="password" name="password" placeholder="Password" required><br>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required><br>
        

            
            <label>Gender:</label>
            <input type="radio" id="male" name="gender" value="male">
            <label for="male">Male</label>
            <input type="radio" id="female" name="gender" value="female">
            <label for="female">Female</label><br><br>
        
            <input type="number" name="basic_pay" placeholder="basic_pay" id="basic_pay" required>
        <button type="submit">Calculate</button>
    </form>
    
    <?php if (isset($Net_pay)): ?>
        <h3>Results:</h3>
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
</body>
</html>
