/* global oidcAdmin, jQuery */
(function ($) {
    'use strict';

    $(document).ready(function () {

        // ---- Discovery abrufen ----
        $('#oidc-fetch-discovery').on('click', function () {
            var $btn    = $(this);
            var $status = $('#oidc-discovery-status');
            var url     = $('#oidc_discovery_url').val().trim();

            if (!url) {
                $status.css('color', '#d63638').text(oidcAdmin.i18n.error);
                return;
            }

            $btn.prop('disabled', true);
            $status.css('color', '#757575').text(oidcAdmin.i18n.fetching);

            $.post(oidcAdmin.ajaxUrl, {
                action: 'oidc_fetch_discovery',
                nonce:  oidcAdmin.nonce,
                url:    url
            })
            .done(function (response) {
                if (response.success) {
                    var d = response.data;
                    if (d.authorization_endpoint) {
                        $('#oidc_authorization_endpoint').val(d.authorization_endpoint);
                    }
                    if (d.token_endpoint) {
                        $('#oidc_token_endpoint').val(d.token_endpoint);
                    }
                    if (d.userinfo_endpoint) {
                        $('#oidc_userinfo_endpoint').val(d.userinfo_endpoint);
                    }
                    if (d.jwks_uri) {
                        $('#oidc_jwks_uri').val(d.jwks_uri);
                    }
                    // PKCE-Checkbox automatisch setzen basierend auf Provider-Support
                    $('#oidc_pkce_supported').prop('checked', d.pkce_supported === true);
                    if (d.issuer) {
                        $('#oidc_issuer').val(d.issuer);
                    }
                    if (d.end_session_endpoint) {
                        $('#oidc_end_session_endpoint').val(d.end_session_endpoint);
                    }
                    $status.css('color', '#00a32a').text(oidcAdmin.i18n.success);
                } else {
                    var msg = (response.data && response.data.message)
                        ? response.data.message
                        : oidcAdmin.i18n.error;
                    $status.css('color', '#d63638').text(msg);
                }
            })
            .fail(function () {
                $status.css('color', '#d63638').text(oidcAdmin.i18n.error);
            })
            .always(function () {
                $btn.prop('disabled', false);
            });
        });

        // ---- JWKS-Cache leeren ----
        $('#oidc-clear-cache').on('click', function () {
            var $btn    = $(this);
            var $status = $('#oidc-cache-status');

            $btn.prop('disabled', true);
            $status.css('color', '#757575').text('…');

            $.post(oidcAdmin.ajaxUrl, {
                action: 'oidc_clear_cache',
                nonce:  oidcAdmin.cacheNonce
            })
            .done(function (response) {
                if (response.success) {
                    $status.css('color', '#00a32a').text(oidcAdmin.i18n.cacheCleared);
                } else {
                    $status.css('color', '#d63638').text(oidcAdmin.i18n.cacheError);
                }
            })
            .fail(function () {
                $status.css('color', '#d63638').text(oidcAdmin.i18n.cacheError);
            })
            .always(function () {
                $btn.prop('disabled', false);
                setTimeout(function () { $status.text(''); }, 3000);
            });
        });

    });
}(jQuery));
