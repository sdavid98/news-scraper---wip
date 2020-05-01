$(document).ready(function () {
    function resizeFooterBottomPadding() {
        var fixedFooterHeight = $('.fixed-footer').outerHeight();
        var paddingBottom = fixedFooterHeight + 13 + 'px';
        $('.footer-bottom').css('padding-bottom', paddingBottom);
    }
    resizeFooterBottomPadding();

    function makeHomepageCardsEvenHeight() {
        var maxHeight = -1;

        if ($('.product-card__body')) {
            $('.product-card__body').each(function() {
                $(this).height('auto');
                maxHeight = maxHeight > $(this).height() ? maxHeight : $(this).height();
            });

            $('.product-card__body').each(function() {
                $(this).height(maxHeight);
            });
        }
    }
    makeHomepageCardsEvenHeight();

    (function makeFooterAbsoluteOnSmallContent() {
        var documentHeight = $(document).height();
        var bodyHeight = $('body').height();
        if (documentHeight > bodyHeight) {
            $('footer').css('margin-top', documentHeight - bodyHeight + 'px');
        }
    })();

    $(window).on('resize', function () {
        resizeFooterBottomPadding();
        makeHomepageCardsEvenHeight();
    });

    function validateEmailInput(email) {
        if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email.val())) {
            if ($('.invalid-feedback--email').hasClass('active')) $('.invalid-feedback--email').removeClass('active');
            return true;
        }
        $('.invalid-feedback--email').addClass('active');
        return false;
    }

    function validateTextInput(textarea) {
        if ($.trim(textarea.val()) !== '') {
            if ($('.invalid-feedback--text').hasClass('active')) $('.invalid-feedback--text').removeClass('active');
            return  true;
        }
        $('.invalid-feedback--text').addClass('active');
        return false;
    }

    var contactForm = $('#contact-form');
    if (contactForm.length === 1) {
        contactForm[0].addEventListener('submit', function(event) {
            event.preventDefault();
            event.stopPropagation();
            var validEmail = validateEmailInput(contactForm.find('input[type="email"]'));
            var validText = validateTextInput(contactForm.find('textarea'));

            if (validEmail && validText) {
                var formData = new FormData();
                formData.append('email', contactForm.find('input[type="email"]').val());
                formData.append('message', contactForm.find('textarea').val());

                $.ajax({
                    url : "../mail.php",
                    type: "POST",
                    data : formData,
                    contentType: false,
                    processData: false,
                    success: function(data)
                    {
                        data = JSON.parse(data);
                        if (data.status === 'false') {
                            $('#status').text(data.text);
                        }
                        else {
                            contactForm.trigger('reset');
                            $('#status').text('Köszönjük megkeresését!');
                        }
                    },
                    error: function (info)
                    {
                        $('#status').text('Hiba történt.');
                    }
                });
            }
        }, false);
    }



    $('.fixed-footer__cookie-button').on('click', function(event) {
        event.preventDefault();

        setCookie("ocuvane_cookie_consent", "true", 3650 );
        getGTMscript();
        setSwitchBasedOnCookie();
        closeCookieBanner();
    });

    function closeCookieBanner(){
        $('.fixed-footer__cookie-info').slideUp({
            complete: function() {
                resizeFooterBottomPadding();
            }
        });
    }

    function getGTMscript() {
        var gtmScript = document.createElement("script");
        gtmScript.id="gtm";
        gtmScript.src = "https://www.googletagmanager.com/gtag/js?id=UA-3895004-126";
        document.head.appendChild(gtmScript);
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());
        gtag('config', 'UA-3895004-126');
    }

    var consentCookie = getCookie("ocuvane_cookie_consent");
    if (consentCookie === "true") {
        getGTMscript();
    } else {
        $('.fixed-footer__cookie-info').slideDown({
            complete: function () {
                resizeFooterBottomPadding();
            }
        });
    }

    function getCookie(cname) {
        var name = cname + "=";
        var decodedCookie = decodeURIComponent(document.cookie);
        var ca = decodedCookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        return "";
    }

    function setCookie(cname, cvalue, exdays) {
        var d = new Date();
        d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
        var expires = "expires=" + d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }

    function setSwitchBasedOnCookie(){
        if (getCookie("ocuvane_cookie_consent") === "true") {
            $(".switch input").prop( "checked", true );
        }else{
            $(".switch input").prop( "checked", false );
        }
    }
    setSwitchBasedOnCookie();

    $(".switch").click(function (event) {
        if($(this).find("input").prop("checked") == true){
            setCookie("ocuvane_cookie_consent", "true", 3650 );
            getGTMscript();
            closeCookieBanner();
        }else if($(this).find("input").prop("checked") == false){
            setCookie("ocuvane_cookie_consent", "false", 3650);
        }
    });

});
