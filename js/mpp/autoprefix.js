var MPPAUTOPREFIX = MPPAUTOPREFIX || {
    "replicated_site_name": "",
    "mpp_base_url": "",
    "site_url": ""
};

MPPAUTOPREFIX.get_url_selector = function (domain) {
    return [
        "a[href^='http://" + domain + "']",
        "a[href^='http://www." + domain + "']",
        "a[href^='https://" + domain + "']",
        "a[href^='https://www." + domain + "']",
    ].join(",");
};

jQuery(window).load(function () {
    if (MPPAUTOPREFIX.replicated_site_name) {
        jQuery(MPPAUTOPREFIX.get_url_selector(MPPAUTOPREFIX.site_url))
            .jurlp('watch', function (e) {
                jQuery(e).jurlp("host", MPPAUTOPREFIX.replicated_site_name + "." + MPPAUTOPREFIX.site_url);
            })
            .jurlp("host", MPPAUTOPREFIX.replicated_site_name + "." + MPPAUTOPREFIX.site_url);

        jQuery(MPPAUTOPREFIX.get_url_selector(MPPAUTOPREFIX.mpp_base_url))
            .jurlp('watch', function (e) {
                jQuery(e).jurlp("host", MPPAUTOPREFIX.replicated_site_name + "." + MPPAUTOPREFIX.mpp_base_url);
            })
            .jurlp("host", MPPAUTOPREFIX.replicated_site_name + "." + MPPAUTOPREFIX.mpp_base_url);
    }
});