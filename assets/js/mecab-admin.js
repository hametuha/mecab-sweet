/*!
 * MeCabSweet's Admin Helper script
 */

(function ($) {
    'use strict';


    $(document).ready(function(){

        // Open example codes
        $('.example-toggle').click(function(e){
            e.preventDefault();
            $('div.example').toggleClass('toggle');
        });

        // CSV create
        $('#mecab-csv-installer').click(function(e){
            e.preventDefault();
            $.get($(this).attr('href'))
                .done(function(result){
                    if( result.success ){
                        $('#user_dic_path').val(result.message);
                    }else{
                        alert(result.message);
                    }
                })
                .fail(function(XMLHttpRequest, textStatus, errorThrown){
                    alert(errorThrown);
                });
        });

        // Build index
        var MF = {

            /**
             * @type {Date}
             */
            start: null,

            /**
             * Main Form
             */
            form: $('#mecab-index-building-form'),

            /**
             * Do Request
             *
             * @param result
             */
            doRequest: function(result){
                $('#indicator-bar').css('width', result.ratio);
                if( result.finished ){
                    $('#form-message').removeClass('loading');
                    MF.resetForm();
                    MF.form.addClass('finished')
                        .find('input[type=submit]').attr('disabled', false);
                }else{
                    MF.form.find('input[name=offset]').val(result.offset);
                    MF.form.ajaxSubmit({
                        success: MF.doRequest,
                        error: MF.errorHandler
                    });
                }
                MF.addMessage(result.message);
            },

            /**
             * Reset form
             */
            resetForm: function(){
                this.form.find('input[name=offset]').val('0');
                $('#form-message').empty();
            },

            /**
             * Add message
             *
             * @param {String} msg
             * @param {Boolean} error
             */
            addMessage: function(msg, error){
                var tag = $(document.createElement(error ? 'strong' : 'span')),
                    box = $('#form-message'),
                    passed = Math.floor( ((new Date()).getTime() - MF.start.getTime()) / 1000 );
                tag.text( '[' + passed + 'sec] ' +  msg).appendTo(box);
                box.scrollTop(box.get(0).scrollHeight);
            },

            /**
             *
             */
            errorHandler: function(){
                $('#form-message').removeClass('loading');
                MF.addMessage('Error!', true);
                MF.form.attr('diabled', false);
            }
        };
        if( MF.form.length ){
            MF.form.submit(function(e){
                e.preventDefault();
                MF.form.find('input[type=submit]').attr('disabled', true);
                $('#indicator-bar').css('width', 0);
                $('#form-message').addClass('loading');
                MF.start = new Date();
                MF.form.ajaxSubmit({
                        success: MF.doRequest,
                        error: MF.errorHandler
                    });
            });
        }

        //
        // Performance
        //
        $('#mecab-performance').submit(function(e){
            e.preventDefault();
            MF.start = new Date();
            MF.resetForm();
            $('#form-message').addClass('loading');
            $(this).ajaxSubmit({
                success: function (result) {
                    $('#form-message').removeClass('loading');
                    $.each(result.message.split("\n"), function (index, elt) {
                        MF.addMessage(elt);
                    });
                }
            });
        });


        //
        // Token search
        //
        var addTermSearchMessage = function(msg, error){
            var p = $('<p></p>');
            if( error ){
                p.addClass('error');
            }
            p.text(msg);
            $('#token-result').append(p);
            setTimeout(function(){
                p.remove();
            }, 3000);
        };
        $(document).on('click', '#cost-exec', function(e){
            e.preventDefault();
            var term = $('#cost-calc').val(),
                container = $('#token-result');
            if( term.length ){
                container.empty();
                $.get($(this).attr('href'), {
                    s: term
                }).done(function(result){
                    if( result.tokens.length ){
                        $.each(result.tokens, function(index, token){
                            container.append($('<a href="#" data-cost="' + token.cost + '"></a>')
                                .text(token.surface + ' (' + token.cost + ')'));
                        });
                    }else{
                        addTermSearchMessage(result.message, true);
                    }
                }).fail(function(xhr, status, error){
                    addTermSearchMessage(error, true);
                });
            }
        });

        $('#token-result').on('click', 'a', function(e){
            e.preventDefault();
            $('#cost').val($(this).attr('data-cost'));
        });


        //
        // Edit screen
        //
        var showMorphemeEditorMessage = function(msg, error){
            var editor = $('#morphem-editor'),
                div = $('<div><p></p></div>');
            editor.prev('div.error, div.updated').remove();
            if( !$.isArray(msg) ){
                msg = [msg];
            }
            div.addClass(error ? 'error' : 'updated');
            div.find('p').html(msg.join('<br />'));
            editor.before(div);
        };
        $('#morphem-editor').submit(function(e){
            e.preventDefault();
            var form = $(this);
            form.ajaxSubmit({
                success: function(result){
                    showMorphemeEditorMessage(result.message, !result.success);
                    if( result.reset ){
                        form.get(0).reset();
                    }
                },
                error: function(xhr, status, error){
                    showMorphemeEditorMessage(error, true);
                }
            });
        });


        //
        // Compiler
        //
        $('.dictionary-compiler').click(function(e){
            e.preventDefault();
            var $btn = $(this), label,
                showMsg = function(m, error){
                    var $span = $('<span></span>');
                    $span.addClass( error ? 'alert' : 'warning').text(m);
                    $btn.next('img').after($span);
                    $span.effect('highlight', 300, function(){
                        setTimeout(function(){
                            $span.fadeOut(500, function(){
                                $(this).remove();
                            });
                        }, 5000);
                    });
                };
            if( !$btn.attr('disabled') ){
                // Change display
                $btn.attr('disabled', true).addClass('disabled');
                label = $btn.text();
                $btn.text($btn.attr('data-loading'));
                $btn.attr('data-loading', label);
                // Ajax
                $.post($btn.attr('data-endpoint'), {
                    _wpnonce: $btn.attr('data-nonce')
                }).done(function(result){
                    showMsg(result.message, !result.success);
                }).fail(function(xhr, status, msg){
                    showMsg(msg, true);
                }).always(function(){
                    $btn.attr('disabled', false).removeClass('disabled');
                    label = $btn.text();
                    $btn.text($btn.attr('data-loading'));
                    $btn.attr('data-loading', label);
                });
            }
        });


        // Check
        $('#check-user-dic').submit(function(e){
            e.preventDefault();
            var pre = $(this).find('pre');
            pre.empty();
            $(this).ajaxSubmit({
                success: function(result){
                    $.each(result.tokens, function(index, elt){
                        var span = $('<span></span>'),
                            strong = $('<strong></strong>');
                        span.text(':' + elt.feature);
                        strong.text(elt.surface);
                        span.prepend(strong);
                        pre.append(span);
                        pre.append('<br />');
                    });
                }
            });
        });
    });


})(jQuery);
