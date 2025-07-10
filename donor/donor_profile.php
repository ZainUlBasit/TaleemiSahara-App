<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../config/database.php';

// Check if the user is logged in and is a donor
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'donor') {
    header("Location: ../index.php");
    exit();
}

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $organization = sanitize($_POST['organization']);
    $phone = sanitize($_POST['phone']);
    $address = sanitize($_POST['address']);
    $website = sanitize($_POST['website']);
    $contact_person = sanitize($_POST['contact_person']);
    $donation_preferences = sanitize($_POST['donation_preferences']);
    $annual_budget = sanitize($_POST['annual_budget']);

    // Validate input
    if (empty($organization) || empty($phone) || empty($address)) {
        $error = "Organization, Phone, and Address are required fields";
    } else {
        // Check if the profile exists
        $stmt = $conn->prepare("SELECT * FROM donor_profiles WHERE user_id = ?");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $profile = $result->fetch_assoc();
        $stmt->close();

        if ($profile) {
            // Profile exists, update it
            $stmt = $conn->prepare("UPDATE donor_profiles SET organization = ?, phone = ?, address = ?, website = ?, contact_person = ?, donation_preferences = ?, annual_budget = ? WHERE user_id = ?");
            $stmt->bind_param("sssssssi", $organization, $phone, $address, $website, $contact_person, $donation_preferences, $annual_budget, $_SESSION['user_id']);
        } else {
            // Profile does not exist, insert it
            $stmt = $conn->prepare("INSERT INTO donor_profiles (user_id, organization, phone, address, website, contact_person, donation_preferences, annual_budget) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssssss", $_SESSION['user_id'], $organization, $phone, $address, $website, $contact_person, $donation_preferences, $annual_budget);
        }

        if ($stmt->execute()) {
            $success = "Profile updated successfully!";
        } else {
            $error = "Failed to update profile: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Fetch existing profile data
$stmt = $conn->prepare("SELECT * FROM donor_profiles WHERE user_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$profile = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300..700&family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400..700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="../css/font.css">
    <link rel="stylesheet" href="../css/general.css">
    <title>Donor Profile</title>
    <style>
        body {
            font-family: 'Raleway', sans-serif;
            display: flex;
            flex-direction: column;
            gap: 100px;
            width: 100vw;
        }

        form {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            column-gap: 20px;
            row-gap: 5px;
            width: 100%;
            margin-top: 10px;

            button {
                grid-column: 1 / -1;
            }
        }

        @media (max-width: 768px) {
            form {
                grid-template-columns: repeat(1, 1fr);
                margin-top: 10px;
            }
        }

        .main {
            margin: 0px auto;
            max-width: 900px;
            width: 100%;
            padding: 20px;
            border: 2px solid #465462;
            margin-top: 100px;
            border-radius: 10px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #333;
        }

        input[type="text"],
        input[type="tel"],
        input[type="url"],
        input[type="number"],
        textarea {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
            font-size: 1rem;
            border: 1.5px solid #465462;
            border-radius: 3px;
        }

        textarea {
            resize: vertical;
            min-height: 80px;
        }

        button {
            padding: 10px 15px;
            background-color: #e74c3c;
            color: white;
            border: none;
            cursor: pointer;
            font-weight: 500;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #c0392b;
        }

        .error {
            color: #e74c3c;
            background: #fdf2f2;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        .success {
            color: #27ae60;
            background: #f0f9f0;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        .required {
            color: #e74c3c;
        }
    </style>
</head>

<body>
    <?php include '../components/donor-navbar.php'; ?>
    <div class="main animate-fade-in">
        <h1>Donor Profile</h1>
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="organization">Organization Name <span class="required">*</span>:</label>
                <input type="text" id="organization" name="organization" value="<?php echo isset($profile['organization']) ? $profile['organization'] : ''; ?>" required disabled>
            </div>
            <div class="form-group">
                <label for="phone">Phone Number <span class="required">*</span>:</label>
                <input type="tel" id="phone" name="phone" value="<?php echo isset($profile['phone']) ? $profile['phone'] : ''; ?>" required disabled>
            </div>

            <div class="form-group">
                <label for="website">Website:</label>
                <input type="url" id="website" name="website" value="<?php echo isset($profile['website']) ? $profile['website'] : ''; ?>" disabled>
            </div>
            <div class="form-group">
                <label for="contact_person">Contact Person:</label>
                <input type="text" id="contact_person" name="contact_person" value="<?php echo isset($profile['contact_person']) ? $profile['contact_person'] : ''; ?>" disabled>
            </div>
            <div class="form-group">
                <label for="address">Address <span class="required">*</span>:</label>
                <textarea id="address" name="address" required disabled><?php echo isset($profile['address']) ? $profile['address'] : ''; ?></textarea>
            </div>
            <div class="form-group">
                <label for="donation_preferences">Donation Preferences:</label>
                <textarea id="donation_preferences" name="donation_preferences" disabled><?php echo isset($profile['donation_preferences']) ? $profile['donation_preferences'] : ''; ?></textarea>
            </div>
            <div class="form-group">
                <label for="annual_budget">Annual Budget (Rs.):</label>
                <input type="number" id="annual_budget" name="annual_budget" step="0.01" min="0" value="<?php echo isset($profile['annual_budget']) ? $profile['annual_budget'] : ''; ?>" disabled>
            </div>
            <button type="button" id="editProfileButton">Edit Profile</button>
            <button type="submit" id="updateProfileButton" style="display: none;">Update Profile</button>
        </form>
    </div>

    <script>
        document.getElementById('editProfileButton').addEventListener('click', function() {
            const inputs = document.querySelectorAll('input, textarea');
            inputs.forEach(input => {
                input.disabled = false;
            });
            document.getElementById('editProfileButton').style.display = 'none';
            document.getElementById('updateProfileButton').style.display = 'block';
        });
    </script>
</body>

</html>