<!DOCTYPE html>
<html>
<head>
    <title>MyGallery</title>
    <link rel="stylesheet" href="/js/extjs/resources/css/ext-all.css" >
    <link rel="stylesheet" href="/css/main.css" >
    <script type="text/javascript" src="/js/extjs/ext-debug.js"></script>
    <script type="text/javascript" src="/js/custom/images.js"></script>

</head>
<body>
<div class='wrapper'>
    <div class="center">
        <h2>MyGallery</h2>
        <div class="main">
            <div id="filecount"></div>
            <div id="fileupload"></div>
            <div id="gallery"></div>
            <div id="imageGrid"></div>
        </div>
    </div>
    <div class="right">
        <fieldset>
            <p>Welcome, <?php echo $username;?></p>
            <a href="/home/logout" title="Logout">Logout</a>
        </fieldset>
    </div>

</div>

</body>
</html>
