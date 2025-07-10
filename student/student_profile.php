<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../config/database.php';

// Check if the user is logged in and is a student
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'student') {
    header("Location: ../index.php");
    exit();
}

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($_POST['name']);
    $father_name = sanitize($_POST['father_name']);
    $roll_no = sanitize($_POST['roll_no']);
    $student_id = sanitize($_POST['student_id']);
    $department = sanitize($_POST['department']);
    $supporters = sanitize($_POST['supporters']);
    $relatives = sanitize($_POST['relatives']);
    $cgpa = sanitize($_POST['cgpa']);
    $sgpa = sanitize($_POST['sgpa']);
    $previous_semester_gpa = sanitize($_POST['previous_semester_gpa']);

    // Handle file upload
    $utility_bills = '';
    if (isset($_FILES['utility_bills']) && $_FILES['utility_bills']['error'] == 0) {
        $allowed = array('pdf', 'jpg', 'jpeg', 'png');
        $filename = $_FILES['utility_bills']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);

        if (in_array(strtolower($filetype), $allowed)) {
            $new_filename = 'utility_bill_' . $_SESSION['user_id'] . '_' . time() . '.' . $filetype;
            $upload_dir = dirname(__FILE__) . '/../uploads/utility_bills/';
            $upload_path = $upload_dir . $new_filename;

            // Create directory if it doesn't exist
            if (!is_dir($upload_dir)) {
                if (!mkdir($upload_dir, 0777, true)) {
                    $error = "Failed to create upload directory. Please contact administrator.";
                }
            }

            if (empty($error)) {
                if (move_uploaded_file($_FILES['utility_bills']['tmp_name'], $upload_path)) {
                    $utility_bills = $new_filename;
                } else {
                    $error = "Failed to upload file. Please try again.";
                }
            }
        } else {
            $error = "Invalid file type. Please upload PDF, JPG, JPEG, or PNG files only.";
        }
    }

    // Validate input
    if (empty($name) || empty($father_name) || empty($roll_no) || empty($department) || empty($student_id)) {
        $error = "All fields are required";
    } else {
        // Check if the profile exists
        $stmt = $conn->prepare("SELECT * FROM student_profiles WHERE user_id = ?");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $profile = $result->fetch_assoc();
        $stmt->close();

        if ($profile) {
            // Profile exists, update it
            $stmt = $conn->prepare("UPDATE student_profiles SET name = ?, father_name = ?, roll_no = ?, student_id = ?, department = ?, supporters = ?, relatives = ?, cgpa = ?, sgpa = ?, previous_semester_gpa = ?, utility_bills = ? WHERE user_id = ?");
            $stmt->bind_param("sssssssssssi", $name, $father_name, $roll_no, $student_id, $department, $supporters, $relatives, $cgpa, $sgpa, $previous_semester_gpa, $utility_bills, $_SESSION['user_id']);
        } else {
            // Profile does not exist, insert it
            $stmt = $conn->prepare("INSERT INTO student_profiles (user_id, name, father_name, roll_no, student_id, department, supporters, relatives, cgpa, sgpa, previous_semester_gpa, utility_bills) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssssssssss", $_SESSION['user_id'], $name, $father_name, $roll_no, $student_id, $department, $supporters, $relatives, $cgpa, $sgpa, $previous_semester_gpa, $utility_bills);
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
$stmt = $conn->prepare("SELECT * FROM student_profiles WHERE user_id = ?");
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
    <title>Student Profile</title>
    <style>
        body {
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

        @media (max-width: 530px) {
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
        }

        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
            font-size: 1rem;
            border: 1.5px solid #465462;
            border-radius: 3px;
        }

        button {
            padding: 10px 15px;
            background-color: #465462;
            color: white;
            border: none;
            cursor: pointer;
        }

        .error {
            color: red;
        }

        .success {
            color: green;
        }
    </style>
</head>

<body>
    <?php include '../components/student-navbar.php'; ?>
    <div class="main animate-fade-in">
        <h1>Student Profile</h1>
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?php echo isset($profile['name']) ? $profile['name'] : ''; ?>" required disabled>
            </div>
            <div class="form-group">
                <label for="father_name">Father Name:</label>
                <input type="text" id="father_name" name="father_name" value="<?php echo isset($profile['father_name']) ? $profile['father_name'] : ''; ?>" required disabled>
            </div>
            <div class="form-group">
                <label for="roll_no">Roll No:</label>
                <input type="text" id="roll_no" name="roll_no" value="<?php echo isset($profile['roll_no']) ? $profile['roll_no'] : ''; ?>" required disabled>
            </div>
            <div class="form-group">
                <label for="student_id">Student ID:</label>
                <input type="text" id="student_id" name="student_id" value="<?php echo isset($profile['student_id']) ? $profile['student_id'] : ''; ?>" required disabled>
            </div>
            <div class="form-group">
                <label for="department">Department:</label>
                <input type="text" id="department" name="department" value="<?php echo isset($profile['department']) ? $profile['department'] : ''; ?>" required disabled>
            </div>
            <div class="form-group">
                <label for="supporters">Supporters:</label>
                <input type="text" id="supporters" name="supporters" value="<?php echo isset($profile['supporters']) ? $profile['supporters'] : ''; ?>" disabled>
            </div>
            <div class="form-group">
                <label for="relatives">Relatives (Brothers and Sisters):</label>
                <input type="number" id="relatives" name="relatives" value="<?php echo isset($profile['relatives']) ? $profile['relatives'] : ''; ?>" disabled>
            </div>
            <div class="form-group">
                <label for="cgpa">CGPA:</label>
                <input type="number" id="cgpa" name="cgpa" step="0.01" value="<?php echo isset($profile['cgpa']) ? $profile['cgpa'] : ''; ?>" disabled>
            </div>
            <div class="form-group">
                <label for="sgpa">SGPA:</label>
                <input type="number" id="sgpa" name="sgpa" step="0.01" value="<?php echo isset($profile['sgpa']) ? $profile['sgpa'] : ''; ?>" disabled>
            </div>
            <div class="form-group">
                <label for="previous_semester_gpa">Previous Semester GPA:</label>
                <input type="number" id="previous_semester_gpa" name="previous_semester_gpa" step="0.01" value="<?php echo isset($profile['previous_semester_gpa']) ? $profile['previous_semester_gpa'] : ''; ?>" disabled>
            </div>
            <div class="form-group">
                <label for="utility_bills">Utility Bills (Upload Document):</label>
                <input type="file" id="utility_bills" name="utility_bills" accept=".jpg,.jpeg,.png" onchange="previewFile(this)" disabled />
                <br />
                <small>Accepted formats: JPG, JPEG, PNG</small>
                <div id="preview-container" style="margin-top: 10px;">
                    <img id="preview-image" src="../uploads/utility_bills/<?php echo $profile['utility_bills']; ?>" style="width: 300px; height: 300px; border: 1px solid #ddd; border-radius: 4px; padding: 5px; ">
                </div>
            </div>
            <button type="button" id="editProfileButton">Edit Profile</button>
            <button type="submit" id="updateProfileButton" style="display: none;">Update Profile</button>
        </form>
    </div>

    <script>
        document.getElementById('editProfileButton').addEventListener('click', function() {
            const inputs = document.querySelectorAll('input');
            inputs.forEach(input => {
                input.disabled = false;
            });
            document.getElementById('editProfileButton').style.display = 'none';
            document.getElementById('updateProfileButton').style.display = 'block';
        });

        function previewFile(input) {
            const preview = document.getElementById('preview-image');
            const previewContainer = document.getElementById('preview-container');
            const file = input.files[0];

            // Clear previous preview
            preview.src = '';
            previewContainer.style.display = 'none';

            if (file) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    // Check if file is an image
                    if (file.type.startsWith('image/')) {
                        preview.src = e.target.result;
                        previewContainer.style.display = 'block';
                    } else {
                        // If it's a PDF, show a PDF icon or message
                        previewContainer.style.display = 'none';
                    }
                }

                reader.readAsDataURL(file);
            }
        }

        // Clear preview when form is reset
        document.querySelector('form').addEventListener('reset', function() {
            const preview = document.getElementById('preview-image');
            const previewContainer = document.getElementById('preview-container');
            preview.src = '';
            previewContainer.style.display = 'none';
        });
    </script>
</body>

</html>