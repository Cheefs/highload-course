<?php
define( 'ROOT_PATH', $_SERVER['DOCUMENT_ROOT']);

$currentPath = $_POST['path']?? DIRECTORY_SEPARATOR; // сохранение пути, если выбрали файл
$dir = $_POST['name'] && is_dir($_POST['name'])? $_POST['name'] : $currentPath;
$recursiveDir = new RecursiveDirectoryIterator($dir.'/');

$selectedFile = null;

if (is_file($_POST['name'])) {

    $selectedFile = $_POST['name'];
    $file = explode(DIRECTORY_SEPARATOR, $selectedFile);
    $file = $file[count($file) - 1];


    copy($selectedFile,ROOT_PATH.'/src/'.$file);
    $selectedFile = $_SERVER['DOCUMENT_ROOT'].'src/'.$file;
}

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="/src/style.css">
    <title>Document</title>
</head>
<body>
    <form method="post">
        <?php foreach($recursiveDir as $file):
            $isDir = $file->isDir();
            $path = $file->getRealPath();
            $fileName = $file->getFilename();
        ?>
            <?php if ( $fileName == '.'): ?>
                <h3><?= $file->getPath() ?> </h3>
                <input hidden value="<?= $file->getPath() ?>" name="path">
            <?php elseif( $fileName == '..'): ?>
                <div class="header">
                    <input class="input" type="submit" value="<?= $path ?>" name="name"><br>
                </div>

            <?php elseif( trim($file->getRealPath()) !== ''): ?>
            <div class="list">
                <label class="<?= $isDir? 'show' : 'hide' ?>" for="<?= $path ?>">&#8226;</label>
                <input class="<?= $isDir? 'dir': 'file' ?>" type="submit" value="<?= $path ?>" name="name">
            </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </form>

    <div class="preview">
        <object class="obj" data="<?= $selectedFile ?>"></object>
    </div>
</body>
</html>

