/**
 * Description
 */

/*global MeCabWordList: true*/

(function ($) {
    'use strict';

    //
    // Word list screen(Delete)
    //

    $(document).ready(function(){

        var
            $form = $('#mecab-term-list'),

            /**
             *
             * @type {*|HTMLElement}
             */
            $table = $('.widefat', '#mecab-term-list'),

            /**
             * Refresh table
             */
            refreshTable = function(){
                if( $table.find('tbody tr').length < 1 ){
                    window.location.reload();
                }
            },

            /**
             * Add message
             *
             * @param {String} msg
             * @param {Boolean} error
             */
            appendMessage = function(msg, error){
                $table.prev('div.updated, div.error').remove();
                var div = $('<div><p></p></div>');
                div.addClass( error ? 'error' : 'updated' );
                if( !$.isArray(msg) ){
                    msg = [msg];
                }
                $.each(msg, function(index, m){
                    var span = $('<span></span>'),
                        p = div.find('p');
                    span.text(m);
                    p.append(span);
                    if( index + 1 < msg.length ){
                        p.append('<br />');
                    }
                });
                $table.before(div);
                div.effect('highlight', 500);
            };

        // Single row click
        $form.on('click', 'a.delete-term', function(e){
            e.preventDefault();
            if( window.confirm(MeCabWordList.confirm) ){
                var link = $(this);
                $.post(MeCabWordList.endpoint, {
                    _wpnonce: MeCabWordList.nonce,
                    term_id: link.attr('data-term-id')
                }).done(function(result){
                    if( result.success ){
                        link.parents('tr').effect('highlight', 300, function(){
                            $(this).fadeOut(500, function(){
                                $(this).remove();
                            });
                        });
                        refreshTable();
                    }else{
                        appendMessage(result.message, true);
                    }
                }).fail(function(jqXHR, status, errorThrown){
                    appendMessage(errorThrown, true);
                });
            }
        });

        // Bulk Delete
        $form.on('click', '#doaction, #doaction2', function(e){
            e.preventDefault();
            switch( $(this).prev('select').val() ){
                case 'delete':
                    if( window.confirm(MeCabWordList.confirm) ){
                        var termIds = [];
                        $form.find('.word-id-container:checked').each(function(index, input){
                            termIds.push($(input).val());
                        });
                        $.post(MeCabWordList.endpoint, {
                            _wpnonce: MeCabWordList.nonce,
                            term_id: termIds
                        }).done(function(result){
                            if( result.success ) {
                                $($.map(result.deleted, function (termId) {
                                    return '#word-' + termId;
                                }).join(',')).parents('tr').effect('highlight', 300, function () {
                                    $(this).fadeOut(500, function () {
                                        $(this).remove();
                                    });
                                });
                                refreshTable();
                            }
                            if( result.message ){
                                appendMessage(result.message, !result.success);
                            }
                        }).fail(function(jqXHR, status, errorThrown){
                            appendMessage(errorThrown, true);
                        });
                    }
                    break;
                default:
                    // Do nothing
                    break;
            }
        });



    });

})(jQuery);
