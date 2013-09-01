<!DOCTYPE html>
<html>
<head>
    <title>Registration Page</title>
    <link rel="stylesheet" href="/css/main.css" >
</head>
<body>
<div class='wrapper'>
    <div class="center">
        <h2>Registration process</h2>
        <div class="main">
            <p>You have to choose username and password for a new user.</p>
            <?php echo validation_errors(); ?>
            <?php echo form_open('verifylogin/registration',array('id'=>'registrationForm')); ?>
            <fieldset>
                <div class="login-form-textfield">
                    <input type="text" size="20" id="username" name="username">
                    <label for="username">Username</label>
                </div>
                <div class="login-form-textfield">
                    <input type="password" size="20" id="password" name="password">
                    <label for="password">Password</label>
                </div>
                <div class="login-form-textfield">
                    <input type="password" size="20" id="password_conf" name="password_confirm">
                    <label for="password">Password confirmation</label>
                </div>
            </fieldset>
            <input type="submit" value="Register"/>
            </form>
        </div>
    </div>

</div>

</body>
</html>
