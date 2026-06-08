@if ($cookieConsentConfig['enabled'] && !$alreadyConsentedWithCookies)
    @include('cookie-consent::dialogContents')

    <script>
        window.laravelCookieConsent = (function() {

            const COOKIE_VALUE = 1;
            const COOKIE_DOMAIN = '{{ config('session.domain') ?? request()->getHost() }}';

            function consentWithCookies() {
                setCookie('{{ $cookieConsentConfig['cookie_name'] }}', COOKIE_VALUE,
                    {{ $cookieConsentConfig['cookie_lifetime'] }});
                hideCookieDialog();
            }

            function ConsentWithoutCookies() {
                setCookie('{{ $cookieConsentConfig['cookie_name'] }}', 0,
                    {{ $cookieConsentConfig['cookie_lifetime'] }});
                hideCookieDialog();
            }

            function cookieExists(name) {
                return (document.cookie.split('; ').indexOf(name + '=' + COOKIE_VALUE) !== -1);
            }

            function hideCookieDialog() {
                const dialogs = document.getElementsByClassName('js-cookie-consent');

                for (let i = 0; i < dialogs.length; ++i) {
                    dialogs[i].style.display = 'none';
                }
            }

            function setCookie(name, value, expirationInDays) {
                const date = new Date();
                date.setTime(date.getTime() + (expirationInDays * 24 * 60 * 60 * 1000));
                document.cookie = name + '=' + value +
                    ';expires=' + date.toUTCString() +
                    ';domain=' + COOKIE_DOMAIN +
                    ';path=/{{ config('session.secure') ? ';secure' : null }}' +
                    '{{ config('session.same_site') ? ';samesite=' . config('session.same_site') : null }}';
            }

            if (cookieExists('{{ $cookieConsentConfig['cookie_name'] }}')) {
                hideCookieDialog();
            }

            const buttons = document.getElementsByClassName('js-cookie-consent-agree');

            for (let i = 0; i < buttons.length; ++i) {
                buttons[i].addEventListener('click', consentWithCookies);
            }

            const buttons1 = document.getElementsByClassName('js-cookie-consent-disagree');

            for (let i = 0; i < buttons1.length; ++i) {
                buttons1[i].addEventListener('click', ConsentWithoutCookies);
            }

            return {
                consentWithCookies: consentWithCookies,
                hideCookieDialog: hideCookieDialog,
                ConsentWithoutCookies: ConsentWithoutCookies
            };
        })();
    </script>
@endif
