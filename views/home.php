<?php
// File: views/home.php
?>

<?php include __DIR__ . '/../includes/header.php'; ?>

<h1>Welcome to the Announced PU Results System</h1>
<p>This platform allows you to interact with 2011 election data from INEC Nigeria.</p>

<h3>What You Can Do:</h3>
<ul>
    <li><strong>View Polling Unit Results</strong> – Select a polling unit and see party scores</li>
    <li><strong>View LGA Summary</strong> – Choose a Local Government and see total votes by party</li>
    <li><strong>Submit New Results</strong> – Enter and save new results for any polling unit</li>
</ul>

<h3>Navigation</h3>
<ul>
    <li><a href="index.php?page=polling_unit_result">Polling Unit Result</a></li>
    <li><a href="index.php?page=lga_total_result">LGA Total Result</a></li>
    <li><a href="index.php?page=add_result">Add New Result</a></li>
</ul>

<?php include __DIR__ . '/../includes/footer.php'; ?>
