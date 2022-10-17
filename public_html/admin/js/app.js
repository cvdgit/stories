

function pjaxGridDeleteInit() {
  var handler = function() {
    $('.pjax-delete-link').on('click', function(e) {
      e.preventDefault();
      var deleteUrl = $(this).attr('delete-url');
      var pjaxContainer = $(this).attr('pjax-container');
      var result = confirm('Подтверждаете удаление записи?');
      if (result) {
        $.ajax({
          url: deleteUrl,
          type: 'post',
          error: function(xhr, status, error) {
            alert(xhr.responseJSON.message)
            //toastr.error(xhr.responseJSON.message);
          }
        }).done(function(data) {
          if (data && data.success) {
            //toastr.success('Успешно');
            $.pjax.reload('#' + $.trim(pjaxContainer), {timeout: 3000});
          }
          else {
            //toastr.error(data['message'] || 'Неизвестная ошибка');
            alert(data['message'] || 'Неизвестная ошибка');
          }
        });
      }
    });
  }
  handler();
  /*  $(document)
      .off('ready pjax:success', handler)
      .on('ready pjax:success', handler);*/
}

pjaxGridDeleteInit();

$(document)
  .off('pjax:success', pjaxGridDeleteInit)
  .on('pjax:success', pjaxGridDeleteInit);

(function() {
  $.ajaxSetup({
    cache: true
  });
})();

function queryStringToJSON(qs) {
    var pairs = qs.split('&');
    var result = {};
    pairs.forEach(function(p) {

        var pair = p.split('=');

        var key = pair[0];
        key = key.replace(/\+/g, '%20');
        key = decodeURIComponent(key);

        var key_is_array = false;
        if (key.indexOf('[]') !== -1) {
            key = key.slice(0, key.indexOf('[]'));
            key_is_array = true;
        }

        var value = pair[1] || '';

        value = value.replace(/\+/g, '%20');
        value = decodeURIComponent(value);

        if (value.indexOf('&') !== -1) {
            if (!result[key] && key_is_array) {
                value = [queryStringToJSON(value)];
            }
            else {
                value = queryStringToJSON(value);
            }
        }

        if (result[key]) {
            if (Object.prototype.toString.call(result[key]) === '[object Array]') {
                result[key].push(value);
            } else {
                result[key] = [result[key], value];
            }
        } else {
            result[key] = value;
        }

    });
    return JSON.parse(JSON.stringify(result));
}

var Neo = (function(jQuery) {
    "use strict";

    var $ = jQuery;
    var cache = {};

    function getEntities(labelID) {
        labelID = labelID || '';
        if (labelID) {
            labelID = '&label_id=' + labelID;
        }
        return $.getJSON("/admin/index.php?r=neo/entity-list" + labelID);
    }

    function getRelations(entityID) {
        return $.getJSON("/admin/index.php?r=neo/relations-list&entity_id=" + entityID);
    }

    function getRelatedEntities(entityID, relationID, direction) {
        return $.getJSON('/admin/index.php?r=neo/related-entities-list', {'entity_id': entityID, 'relation_id': relationID, 'direction': direction});
    }

    function saveRelations(relations) {
        return $.post('/admin/index.php?r=neo/save-relations', relations);
    }

    function deleteRelation(relation) {
        return $.post('/admin/index.php?r=neo/delete-relation', relation);
    }

    function getLabels() {
        return $.post('/admin/index.php?r=neo/labels');
    }

    function getQuestions(param, paramValue) {
        return $.getJSON('/admin/index.php?r=neo/questions', {"param": param, "value": paramValue});
    }

    function getQuestionList() {
        return $.getJSON('/admin/index.php?r=neo/question-list');
    }

    function questions(questionID, params) {
        params = params || '';
        var data = {'id': questionID};
        if (params.length) {
            data = $.extend(data, queryStringToJSON(params));
        }
        return $.getJSON('/admin/index.php?r=neo/question-get', data);
    }

    function getTaxonList() {
        var key = 'taxonNames';
        if (cache[key]) {
            var def = $.Deferred();
            def.resolve(cache[key]);
            return def.promise();
        }
        return $.getJSON('/admin/index.php?r=neo/taxon-list')
            .done(function(response) {
                cache[key] = response;
            });
    }

    function getTaxonValueList(taxon) {
        var key = taxon;
        if (cache[key]) {
            var def = $.Deferred();
            def.resolve(cache[key]);
            return def.promise();
        }
        return $.getJSON('/admin/index.php?r=neo/taxon-value-list', {"taxon": taxon})
            .done(function(response) {
                cache[key] = response;
            });
    }

    function getQuestionValues(id) {
        return $.getJSON('/admin/index.php?r=neo/question-values', {"id": id});
    }

    return {
        "getEntities": getEntities,
        "getRelations": getRelations,
        "getRelatedEntities": getRelatedEntities,
        "saveRelations": saveRelations,
        "deleteRelation": deleteRelation,
        "getLabels": getLabels,
        "getQuestions": getQuestions,
        "getQuestionList": getQuestionList,
        "questions": questions,
        "getTaxonList": getTaxonList,
        "getTaxonValueList": getTaxonValueList,
        "getQuestionValues": getQuestionValues
    };
})(jQuery);

function yiiModalFormInit(formElement, doneCallback, failCallback, alwaysCallback) {
    formElement.on('beforeSubmit', function(e) {
        e.preventDefault();
        var $btn = $(this).find('button[type=submit]');
        $btn.button('loading');
        $.ajax({
            url: $(this).attr('action'),
            type: $(this).attr('method'),
            data: new FormData(this),
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false
        })
            .done(doneCallback)
            .fail(failCallback)
            .always(function() {
                $btn.button('reset');
                if (typeof alwaysCallback === 'function') {
                    alwaysCallback();
                }
            });
        return false;
    })
        .on('submit', function(e) {
            e.preventDefault();
        });
}

/*
 * printThis v1.5
 * @desc Printing plug-in for jQuery
 * @author Jason Day
 *
 * Resources (based on) :
 *              jPrintArea: http://plugins.jquery.com/project/jPrintArea
 *              jqPrint: https://github.com/permanenttourist/jquery.jqprint
 *              Ben Nadal: http://www.bennadel.com/blog/1591-Ask-Ben-Print-Part-Of-A-Web-Page-With-jQuery.htm
 *
 * Licensed under the MIT licence:
 *              http://www.opensource.org/licenses/mit-license.php
 *
 * (c) Jason Day 2014
 *
 * Usage:
 *
 *  $("#mySelector").printThis({
 *      debug: false,               * show the iframe for debugging
 *      importCSS: true,            * import page CSS
 *      importStyle: false,         * import style tags
 *      printContainer: true,       * grab outer container as well as the contents of the selector
 *      loadCSS: "path/to/my.css",  * path to additional css file - us an array [] for multiple
 *      pageTitle: "",              * add title to print page
 *      removeInline: false,        * remove all inline styles from print elements
 *      printDelay: 333,            * variable print delay
 *      header: null,               * prefix to html
 *      formValues: true            * preserve input/form values
 *  });
 *
 * Notes:
 *  - the loadCSS will load additional css (with or without @media print) into the iframe, adjusting layout
 */
;
(function($) {
    var opt;
    $.fn.printThis = function(options) {
        opt = $.extend({}, $.fn.printThis.defaults, options);
        var $element = this instanceof jQuery ? this : $(this);

        var strFrameName = "printThis-" + (new Date()).getTime();

        if (window.location.hostname !== document.domain && navigator.userAgent.match(/msie/i)) {
            // Ugly IE hacks due to IE not inheriting document.domain from parent
            // checks if document.domain is set by comparing the host name against document.domain
            var iframeSrc = "javascript:document.write(\"<head><script>document.domain=\\\"" + document.domain + "\\\";</script></head><body></body>\")";
            var printI = document.createElement('iframe');
            printI.name = "printIframe";
            printI.id = strFrameName;
            printI.className = "MSIE";
            document.body.appendChild(printI);
            printI.src = iframeSrc;

        } else {
            // other browsers inherit document.domain, and IE works if document.domain is not explicitly set
            var $frame = $("<iframe id='" + strFrameName + "' name='printIframe' />");
            $frame.appendTo("body");
        }


        var $iframe = $("#" + strFrameName);

        // show frame if in debug mode
        if (!opt.debug) $iframe.css({
            position: "absolute",
            width: "0px",
            height: "0px",
            left: "-600px",
            top: "-600px"
        });


        // $iframe.ready() and $iframe.load were inconsistent between browsers
        setTimeout(function() {

            var $doc = $iframe.contents(),
                $head = $doc.find("head"),
                $body = $doc.find("body");

            // add base tag to ensure elements use the parent domain
            $head.append('<base href="' + document.location.protocol + '//' + document.location.host + '">');

            // import page stylesheets
            if (opt.importCSS) $("link[rel=stylesheet]").each(function() {
                var href = $(this).attr("href");
                if (href) {
                    var media = $(this).attr("media") || "all";
                    $head.append("<link type='text/css' rel='stylesheet' href='" + href + "' media='" + media + "'>")
                }
            });

            // import style tags
            if (opt.importStyle) $("style").each(function() {
                $(this).clone().appendTo($head);
                //$head.append($(this));
            });

            //add title of the page
            if (opt.pageTitle) $head.append("<title>" + opt.pageTitle + "</title>");

            // import additional stylesheet(s)
            if (opt.loadCSS) {
                if( $.isArray(opt.loadCSS)) {
                    jQuery.each(opt.loadCSS, function(index, value) {
                        $head.append("<link type='text/css' rel='stylesheet' href='" + this + "'>");
                    });
                } else {
                    $head.append("<link type='text/css' rel='stylesheet' href='" + opt.loadCSS + "'>");
                }
            }

            // print header
            if (opt.header) $body.append(opt.header);

            // grab $.selector as container
            if (opt.printContainer) $body.append($element.outer());

            // otherwise just print interior elements of container
            else $element.each(function() {
                $body.append($(this).html());
            });

            // capture form/field values
            if (opt.formValues) {
                // loop through inputs
                var $input = $element.find('input');
                if ($input.length) {
                    $input.each(function() {
                        var $this = $(this),
                            $name = $(this).attr('name'),
                            $checker = $this.is(':checkbox') || $this.is(':radio'),
                            $iframeInput = $doc.find('input[name="' + $name + '"]'),
                            $value = $this.val();

                        //order matters here
                        if (!$checker) {
                            $iframeInput.val($value);
                        } else if ($this.is(':checked')) {
                            if ($this.is(':checkbox')) {
                                $iframeInput.attr('checked', 'checked');
                            } else if ($this.is(':radio')) {
                                $doc.find('input[name="' + $name + '"][value=' + $value + ']').attr('checked', 'checked');
                            }
                        }

                    });
                }

                //loop through selects
                var $select = $element.find('select');
                if ($select.length) {
                    $select.each(function() {
                        var $this = $(this),
                            $name = $(this).attr('name'),
                            $value = $this.val();
                        $doc.find('select[name="' + $name + '"]').val($value);
                    });
                }

                //loop through textareas
                var $textarea = $element.find('textarea');
                if ($textarea.length) {
                    $textarea.each(function() {
                        var $this = $(this),
                            $name = $(this).attr('name'),
                            $value = $this.val();
                        $doc.find('textarea[name="' + $name + '"]').val($value);
                    });
                }
            } // end capture form/field values

            // remove inline styles
            if (opt.removeInline) {
                // $.removeAttr available jQuery 1.7+
                if ($.isFunction($.removeAttr)) {
                    $doc.find("body *").removeAttr("style");
                } else {
                    $doc.find("body *").attr("style", "");
                }
            }

            setTimeout(function() {
                if ($iframe.hasClass("MSIE")) {
                    // check if the iframe was created with the ugly hack
                    // and perform another ugly hack out of neccessity
                    window.frames["printIframe"].focus();
                    $head.append("<script>  window.print(); </script>");
                } else {
                    // proper method
                    $iframe[0].contentWindow.focus();
                    $iframe[0].contentWindow.print();
                }

                //remove iframe after print
                if (!opt.debug) {
                    setTimeout(function() {
                        $iframe.remove();
                    }, 1000);
                }

            }, opt.printDelay);

        }, 333);

    };

    // defaults
    $.fn.printThis.defaults = {
        debug: false,           // show the iframe for debugging
        importCSS: true,        // import parent page css
        importStyle: false,     // import style tags
        printContainer: true,   // print outer container/$.selector
        loadCSS: "",            // load an additional css file - load multiple stylesheets with an array []
        pageTitle: "",          // add title to print page
        removeInline: false,    // remove all inline styles
        printDelay: 333,        // variable print delay
        header: null,           // prefix to html
        formValues: true        // preserve input/form values
    };

    // $.selector container
    jQuery.fn.outer = function() {
        return $($("<div></div>").html(this.clone())).html()
    }
})(jQuery);
