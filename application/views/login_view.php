<!DOCTYPE html>
<html>
 <head>
   <title>Image Gallery</title>
   <link rel="stylesheet" href="/css/main.css" >
 </head>
 <body>
    <div class='wrapper'>
        <div class="center">
            <h2>Welcome to ImageGallery!</h2>
            <div class="main">
                <p>You can create your own account and view/upload your images.</p>
            </div>
        </div>
        <div class="right">
            <?php echo validation_errors(); ?>
            <?php echo form_open('verifylogin',array('id'=>'loginForm')); ?>
               <fieldset>
                    <div class="login-form-textfield">
                        <input type="text" size="20" id="username" name="username">
                        <label for="username">Username</label>
                    </div>
                    <div class="login-form-textfield">
                        <input type="password" size="20" id="password" name="password">
                        <label for="password">Password</label>
                    </div>
               </fieldset>
                <input type="submit" value="Login"/>
            </form>
            <a href="/login/userRegister" title="Registration">Registration</a>

        </div>
    </div>

</body>
</html>
