<?php

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help & Support - Fleet Management System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --text-color: #333;
            --light-bg: #f8f9fa;
            --dark-bg: #343a40;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            background-color: var(--light-bg);
        }

        body {
            color: var(--text-color);
        }

        .container {
            max-width: 1000px;
            margin: 50px auto;
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.8);
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .back-arrow {
            display: inline-block;
            margin-bottom: 20px;
            color: var(--secondary-color);
            text-decoration: none;
            font-size: 1.2rem;
            transition: color 0.3s;
        }

        .back-arrow:hover {
            color: var(--primary-color);
        }

        .help-content {
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .help-section {
            margin-bottom: 30px;
        }

        .help-section h2 {
            font-size: 2rem;
            margin-bottom: 20px;
            color: var(--primary-color);
            border-bottom: 2px solid var(--secondary-color);
            padding-bottom: 10px;
        }

        .help-section p {
            font-size: 1.1rem;
            line-height: 1.7;
            color: var(--text-color);
        }

        .help-section ul {
            list-style-type: disc;
            padding-left: 20px;
            margin-bottom: 15px;
        }

        .help-section li {
            font-size: 1.1rem;
            line-height: 1.7;
            color: var(--text-color);
        }

        .help-section h3 {
            font-size: 1.4rem;
            color: var(--primary-color);
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="dashboard.php" class="back-arrow"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
        <div class="help-content">
            <h1 class="page-title">Help & Support</h1>
            <div class="help-section">
                <h2>Frequently Asked Questions</h2>
                <ul>
                    <li>
                        <h3>How do I add a new vehicle to the fleet?</h3>
                        <p>
                            To add a new vehicle, navigate to the "Fleet Overview" section and click on the "Add Vehicle" button.  Fill in the required details, such as vehicle type, make, model, and license plate number, and then save.
                        </p>
                    </li>
                    <li>
                        <h3>How do I schedule maintenance for a vehicle?</h3>
                        <p>
                            Go to the "Maintenance" section and select "Schedule Maintenance".  Choose the vehicle, select the maintenance type, specify the date and time, and assign a mechanic.
                        </p>
                    </li>
                    <li>
                        <h3>How do I record fuel consumption?</h3>
                        <p>
                            In the "Fuel Management" section, click on "Record Fuel Usage". Enter the vehicle details, fuel quantity, cost, and date.
                        </p>
                    </li>
                    <li>
                        <h3>How do I assign a driver to a vehicle?</h3>
                        <p>
                            Navigate to the "Drivers" section, select a driver, and then assign them to a vehicle.  Alternatively, you can go to the "Fleet Overview" section, select a vehicle, and assign a driver from there.
                        </p>
                    </li>
                    <li>
                        <h3>How do I view trip logs?</h3>
                        <p>
                            Go to the "Trip Logs" section to see a record of all trips.  You can filter by date, driver, or vehicle.
                        </p>
                    </li>
                </ul>
            </div>

            <div class="help-section">
                <h2>User Roles and Permissions</h2>
                <p>The system has several user roles with different permissions:</p>
                <ul>
                    <li><strong>Admin:</strong> Full access to all features and settings.</li>
                    <li><strong>Manager:</strong> Can manage fleet, maintenance, fuel, drivers, and trip logs.</li>
                    <li><strong>Driver:</strong> Can view assigned trips and alerts.</li>
                    <li><strong>Inventory Officer:</strong> Can manage inventory.</li>
                    <li><strong>Mechanic:</strong> Can view and update assigned maintenance tasks.</li>
                    <li><strong>Accountant:</strong> Can manage fuel costs and generate financial reports.</li>
                </ul>
            </div>

            <div class="help-section">
                <h2>Safety and Health</h2>
                <p>The Fleet Management System includes features to help maintain a safe and healthy work environment:</p>
                <ul>
                    <li><strong>Vehicle Maintenance:</strong> Regular maintenance schedules help ensure vehicles are safe to operate.  See the "Maintenance" section for details.</li>
                    <li><strong>Driver Management:</strong>  Tools for managing driver information, including certifications and training, are available in the "Drivers" section.</li>
                    <li><strong>Trip Logs:</strong>  Trip logs can be used to monitor driver hours and ensure compliance with regulations, promoting driver safety.  See the "Trip Logs" section.</li>
                    <li><strong>Alerts:</strong>  The system provides alerts for potential issues, such as overdue maintenance or expiring certifications, allowing for proactive safety measures.  See the "Alerts" section.</li>
                    <li><strong>Incident Reporting:</strong>  The system allows for recording incidents.</li>
                </ul>
            </div>

            <div class="help-section">
                <h2>Troubleshooting</h2>
                <p>If you encounter any issues, please try the following:</p>
                <ul>
                    <li>Ensure you have a stable internet connection.</li>
                    <li>Clear your browser's cache and cookies.</li>
                    <li>Try using a different web browser.</li>
                    <li>If you are still having problems, contact our support team.</li>
                </ul>
            </div>
            <div class="help-section">
                <h2>Contact Support</h2>
                <p>If you can't find the answer to your question in the FAQ or troubleshooting sections, please contact our support team:</p>
                <p>Email: support@popestr.com</p>
                <p>Phone: +255 784 123 456</p>
            </div>
        </div>
    </div>
</body>
</html>
