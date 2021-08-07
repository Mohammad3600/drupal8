$(function() {
    console.log("helloabcdefgh");
    function matchLinkText(path, machine_name) {
        if (parts[parts.length - 2] == path) {
            $(".users_guide_conducting_an_sm_lcaa ul li a").each(function() {
                var linkTextParts = $(this).attr('href').split("/");
                if (linkTextParts[linkTextParts.length - 1] == parts[parts.length - 1]) {
                    $(this).parent().parent().parent().addClass('active');
                }
            })
        }
    }
    stopwords = ['is', 'as', 'a']
    var url = location.href;
    parts = url.split("/");
    // last_part = parts[parts.length - 1];
    // last_but_one = parts[parts.length - 2];
    $('.lc_taxanomy').hide();
    if (parts[parts.length - 2] == 'learning-center' || parts[parts.length - 3] == 'learning-center') {
        if (parts[parts.length - 1] == 'why-sustainable-minds' || parts[parts.length - 2] == 'why-sustainable-minds') {
            if (parts[parts.length - 2] == 'why-sustainable-minds') {
                $(".why_sustainable_minds_ ul li a").each(function() {
                    var linkTextParts = $(this).attr('href').split("/");
                    if (linkTextParts[linkTextParts.length - 1] == parts[parts.length - 1]) {
                        $(this).parent().parent().parent().addClass('active');
                    }
                })
            }
            $('.why_sustainable_minds_').parent('li').addClass('active');
            $('.why_sustainable_minds_').show();
        }
        if (parts[parts.length - 1] == "users-guide-conducting-sm-lca" || parts[parts.length - 2] == "conducting-sm-lca") {
            $('.users_guide_conducting_an_sm_lcaa').show();
            $('.users_guide_conducting_an_sm_lcaa').parent('li').addClass('active');
            if (parts[parts.length - 2] == 'conducting-sm-lca') {
                $(".users_guide_conducting_an_sm_lcaa ul li a").each(function() {
                    var linkTextParts = $(this).attr('href').split("/");
                    if (linkTextParts[linkTextParts.length - 1] == parts[parts.length - 1]) {
                        $(this).parent().parent().parent().addClass('active');
                    }
                })
            }
        }
        if (parts[parts.length - 1] == "ecodesign-and-lca" || parts[parts.length - 2] == "ecodesign-and-lca") {
            $('.ecodesign_and_lca').show();
            $('.ecodesign_and_lca').parent('li').addClass('active');
            if (parts[parts.length - 2] == 'ecodesign-and-lca') {
                $(".ecodesign_and_lca ul li a").each(function() {
                    var linkTextParts = $(this).attr('href').split("/");
                    if (linkTextParts[linkTextParts.length - 1] == parts[parts.length - 1]) {
                        $(this).parent().parent().parent().addClass('active');
                    }
                })
            }
        }
        if (parts[parts.length - 1] == "ecodesign-strategies" || parts[parts.length - 2] == "ecodesign-strategies") {
            $('.ecodesign_strategies').show();
            $('.ecodesign_strategies').parent('li').addClass('active');
            if (parts[parts.length - 2] == 'ecodesign-strategies') {
                $(".ecodesign_strategies ul li a").each(function() {
                    var linkTextParts = $(this).attr('href').split("/");
                    if (linkTextParts[linkTextParts.length - 1] == parts[parts.length - 1]) {
                        $(this).parent().parent().parent().addClass('active');
                    }
                })
            }
        }
        if (parts[parts.length - 1] == "methodology" || parts[parts.length - 2] == "methodology") {
            $('.sm2013_impact_assessment_methodology').show();
            $('.sm2013_impact_assessment_methodology').parent('li').addClass('active');
            if (parts[parts.length - 2] == 'methodology') {
                $(".sm2013_impact_assessment_methodology ul li a").each(function() {
                    var linkTextParts = $(this).attr('href').split("/");
                    if (linkTextParts[linkTextParts.length - 1] == parts[parts.length - 1]) {
                        $(this).parent().parent().parent().addClass('active');
                    }
                })
            }
        }
    }
    
});