/**
 * Created with JetBrains PhpStorm.
 * User: ptaha
 * Date: 25.08.13
 * Time: 15:43
 * To change this template use File | Settings | File Templates.
 */
Ext.application({
    name: 'HelloExt',
    launch: function() {
        Ext.create('Ext.form.Panel', {
            renderTo: Ext.getBody(),
            title: 'Login Form',
            height: 130,
            width: 280,
            bodyPadding: 10,
            defaultType: 'textfield',
            url: 'verifylogin',
            items: [
                {
                    fieldLabel: 'Username',
                    name: 'username'
                },
                {
                    fieldLabel: 'Password',
                    name: 'password',
                    inputType: 'password'
                }

            ],
            buttons: [
                {
                    text: 'submit',
                    handler: function() {
                        var form = this.up('form').getForm();
                        if (form.isValid()) { // make sure the form contains valid data before submitting
                            form.submit({
                                success: function(form, action) {
                                    Ext.Msg.alert('Success', action.result.msg);
                                },
                                failure: function(form, action) {
                                    Ext.Msg.alert('Failed', action.result.msg);
                                }
                            });
                        } else { // display error alert if the data is invalid
                            Ext.Msg.alert('Invalid Data', 'Please correct form errors.')
                        }
                    }
                }
            ]
        });
    }
});
