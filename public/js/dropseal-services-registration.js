(function( $ ) {
    "use strict";
    $(function() {

        const emailValidation = (input, button) => {
            let pattern = /^([a-z0-9_\.-])+[@][a-z0-9-]+\.([a-z]{2,4}\.)?[a-z]{2,4}$/i;
            $(input).keyup(function() {
            if( $(input).val().search(pattern) == 0 ) {
                $('#valid_email_message').text('');
                $(input).removeAttr('style');
                $(button).attr('disabled', false);
            }else{
                $(button).attr('disabled', true);
            }
            }).blur(function() {
                if( $(input).val().search(pattern) != 0 ) {
                    $('#valid_email_message').text('Invalid Email');
                    $(input).css({'border-color':'#d8512d'});
                }
            });
        }

        emailValidation('input[name="email"]', 'button[name="dss_register"]');
        emailValidation('input[name="email"]', 'button[name="update_user"]');

        // Phone validation
        $('input[name="phone"]').keydown(function(e) {
            // Allow: backspace, delete, tab, escape, enter and .
            if ($.inArray(e.keyCode, [46, 8, 9, 27, 91, 110]) !== -1 ||
                 // Allow: Ctrl+A
                (e.keyCode == 65 && e.ctrlKey === true) ||
                 // Allow: home, end, left, right
                (e.keyCode >= 35 && e.keyCode <= 39)) {
                 // let it happen, don't do anything
                return;
            }
            // Ensure that it is a number and stop the keypress
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }
        });

        const phoneValidation = (input, button) => {
            $(input).keyup(function() {
                let phone =  $(this).val();
                if( phone.length != 10 ) {
                    $('#invalid_phone_message').text('Phone number must be 10 characters.');
                    $(button).attr('disabled', true);
                }else{
                    $('#invalid_phone_message').text('');
                    $(input).removeAttr('style');
                    $(button).attr('disabled', false);
                }
            }).focus(function() {
                if( $(input).val().length != 10 ) {
                    $('#invalid_phone_message').text('Phone number must be 10 characters.');                    
                }
            }).blur(function() {
                if( $(input).val().length != 10 ) {
                    $(input).css({'border-color':'#d8512d'});                 
                }
            });
        }

        phoneValidation('input[name=phone]', 'button[name="dss_register"]');

        // Password validation
        const checkPassword = (input, button) => {
            $(input).keyup(function() {
                let pswd = $(input).val();
                if( pswd.length < 6 ) {
                    $('#valid_password_message').text('Password length not less than 6 characters.');
                    $(button).attr('disabled', true);
                }else{
                    $('#valid_password_message').text('');
                    $(button).attr('disabled', false);
                    $(input).removeAttr('style');
                }
            }).focus(function() {
                if( $(input).val().length < 6 ) {
                    $('#valid_password_message').text('Password length not less than 6 characters.');                    
                }
                if( $('.dss-confirm-pass') ) {
                    $('.dss-confirm-pass').show();
                }
            }).blur(function() {
                if( $(input).val().length < 6 ) {
                    $(input).css({'border-color':'#d8512d'});
                }
            });
        }

        checkPassword('input[name="password"]', 'button[name="dss_register"]');
        checkPassword('#new-password', 'button[name="update_pass"]');

        $('#dss-confirm-password').keyup(function () {
            let pas = $('input[name="new_password"]').val();
            let confirmPas = $(this).val();
            if( pas !== confirmPas) {
                $('#new-password').css('border-color', '#d8512d');
            }else{
                $('#new-password').removeAttr('style');
            }
        });
        
    });

})( jQuery );