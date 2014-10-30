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
                MF.addMessage('Error!', true);
                MF.form.attr('diabled', false);
            }
        };
        if( MF.form.length ){
            MF.form.submit(function(e){
                e.preventDefault();
                MF.form.find('input[type=submit]').attr('disabled', true);
                $('#indicator-bar').css('width', 0);
                MF.start = new Date();
                MF.form.ajaxSubmit({
                        success: MF.doRequest,
                        error: MF.errorHandler
                    });
                MF.form.addClass('loading').removeClass('finished');
            });
        }

        $('#mecab-performance').submit(function(e){
            e.preventDefault();
            MF.start = new Date();
            MF.resetForm();
            $(this).ajaxSubmit({
                success: function (result) {
                    $.each(result.message.split("\n"), function (index, elt) {
                        MF.addMessage(elt);
                    });
                }
            });
        });

    });


})(jQuery);
