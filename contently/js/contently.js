(function ($) {
    $(document).ready(function ($) {

        // Popups
        $(document).on('click', '.cm-popup', function () {
            var element = '#' + $(this).data('block'),
                template = $(element).html(),
                container = $(this).closest('form').find('.cl_postbox');

            container.find('.cl_inside').hide();
            container.append(template);
        });

        $(document).on('click', 'button[data-action="close"]', function () {
            var container = $(this).closest('.cl_postbox');

            container.find('.b-popup').remove();
            container.find('.cl_inside').show();
            return false;
        });


        $('.js-add-api-key').on('click', function () {
            add_api_key();
        });
        $('.js-add-api-key-hidden').on('click', function () {
            add_api_key();
        });

        function add_api_key() {
            var template = $('.js-add-api-key-template').clone();
            template.removeClass('hidden').removeClass('js-add-api-key-template');
            $('.js-last-block').before(template);
        }

        $(document).on('click', '.js-delete-api-key', function () {

            var data = {
                'action': 'delete_api_key',
                'api_key': $(this).closest('.js-profile').find("input[name^='api_keys']").val()
            };
            ajax_request(data, this);
            var profile_to_delete = $(this).closest('.js-profile');
            profile_to_delete.remove();

            if ($('.js-profile').length <= 1) {
                $('.js-add-api-key-hidden').trigger('click');
            }
        });

        $(document).on('click', '.js-open-current-profile', function () {
            open_profile(this);
        });

        $(document).on('click', '.cl-profile-name', function () {
            open_profile(this);
        });

        function open_profile(oThis) {

            var profile = $(oThis).closest('.js-profile');
            var data = {
                'action': 'get_profile_api_key',
                'api_key': profile.find("input[name^='api_keys']").val()
            };

            ajax_request(data, oThis, false, profile);
        }

        $(document).on('click', '.js-close-profile', function () {

            var target_box = $(this).closest('.js-profile').find('.js-profile-options');

            target_box.html('');
            $(this).prev('.js-open-current-profile').show();
            $(this).hide();

        });

        //ajax navigation in profile

        $(document).on('click', '.js-ajax-redirect', function () {

            var profile = $(this).closest('.js-profile');
            var api_key = profile.find('input[name=api_key]').val();
            var action = $(this).data('action');
            var data = {
                'action': action,
                'api_key': api_key,
                'type': $(this).data('type')
            };

            ajax_request(data, this, false, profile);

        });

        //ajax update

        $(document).on('click', '.js-update', function () {

            var profile = $(this).closest('.js-profile');

            var api_key = profile.find('input[name=api_key]').val();


            var data = {
                'action': profile.find('input[name=action]').val(),
                'api_key': api_key,
            };

            $.each(profile.find('select'), function (index, select) {
                var name = $(select).attr("name");
                var val = $(select).val();
                data[name] = val;
            });

            $.each(profile.find('.js-profile-form :input'), function (index, input) {
                var name = $(input).attr("name");
                var val = $(input).val();
                //get data from all inputs except checkboxes
                if ($(input).attr('type') != 'checkbox' && $(input).attr('type') != 'radio') {
                    data[name] = val;
                }
                //get data from all checked checkboxes
                if ($(input).prop('checked')) {
                    data[name] = val;
                }
            });

            data['type'] = profile.find('input[name=target_type]').val();
            if (data.action == 'set_profile_api_key') {
                var api_key_new = profile.find('input[name=api_key_new]').val();
                api_key_new = typeof api_key_new !== 'undefined' ? api_key_new : false;

                data.debug_state = !!$('#debug-mode').attr('checked');

                if (typeof api_key === "undefined") {
                    console.log('undefined api key');
                    return false;
                }
                if (typeof api_key_new === "undefined") {
                    console.log('undefined new api key');
                    return false;
                }

                if (api_key_new != "undefined" && !key_validation(api_key_new)) {

                    profile.find('input[name=api_key_new]').focus();
                    return false;
                }
                data['api_key_new'] = api_key_new;
                var api_key_old = profile.find("input[name^='api_keys']").val();
                if (api_key_new != api_key_old) {
                    profile.find("input[name^='api_keys']").val(api_key_new);
                }
                data['profile_name'] = profile.find("input[name=profile_name]").val();
            }

            ajax_request(data, this, true, profile);

        });

        function ajax_request(data, oThis, updating, profile) {

            updating = typeof updating !== 'undefined' ? updating : false;
            profile = typeof profile !== 'undefined' ? profile : false;
            var target_box;

            target_box = $(oThis).closest('.js-profile').find('.js-profile-options');

            if (updating) {
                $.toast(contently_data.messages.updating + ' <img src="' + contently_data.plugin_url + '/images/1.gif" height="10px" width="10px">', {
                    'type': 'success',
                    'align': 'right-bottom',
                    'duration': 10000000
                });
            }
            $.ajax({
                url: contently_data.admin_url + '/admin-ajax.php',
                type: 'POST',
                data: data,
                success: function (result) {
                    //console.log(result);
                    if (is_json_string(result)) {

                        var parsed = JSON.parse(result);

                        if (typeof parsed.template != 'undefined') {
                            target_box.html(parsed.template);
                        }

                        if (typeof parsed.publication_name != 'undefined' && !$.isEmptyObject(parsed.publication_name)) {
                            $(profile).find('.cl-profile-name').html(parsed.publication_name);
                        }

                        view_messages(parsed, target_box);

                        if (profile) {
                            close_other_profiles(profile);
                        }
                    } else {
                        target_box.html(result);
                    }
                },
                error: function (errorThrown) {
                    console.log(errorThrown);
                }
            });
            return true;

        }

        function view_messages(parsed, target_box) {
            $.toast('', {'type': 'clear', 'align': 'right-bottom'});

            if (typeof parsed.message != 'undefined' && !$.isEmptyObject(parsed.message)) {
                $.toast(parsed.message.text, {'type': parsed.message.type, 'align': 'right-bottom'});
            }

            if (typeof parsed.messages != 'undefined' && !$.isEmptyObject(parsed.messages)) {
                $.each(parsed.messages, function (index, value) {
                    $.toast(value.text, {'type': value.type, 'align': 'right-bottom', 'singleton': false});
                });
            }

            if (typeof parsed.popups != 'undefined' && !$.isEmptyObject(parsed.popups)) {
                target_box.append(parsed.popups);
            }
        }

        function close_other_profiles(profile) {

            var close_button = $(profile).find('.js-close-profile');
            var open_button = $(profile).find('.js-open-current-profile');
            $('.js-close-profile').each(function (index, object) {
                if ($(object).get(0) != close_button.get(0)) {
                    $(object).trigger('click');
                }
            });
            close_button.show();
            open_button.hide();
        }

        function is_json_string(str) {
            try {
                JSON.parse(str);
                return true;
            } catch (e) {
                return false;
            }
        }

        //author settings
        $(document).on('click', '.js-author-settings', function () {
            var target = $(this).data('target');
            if (target == 'blocked') {
                $('.js-author-uid').hide();
                $('.js-author-blocked').show();
            } else {
                $('.js-author-blocked').hide();
                $('.js-author-uid').show();
            }
        });

        var author_autocomplite_options = {
            source: contently_data.users,
            minLength: 2,
            select: function (event, ui) {
                $(".form-autocomplete").val(ui.item.label); //ui.item is your object from the array
                $(".form-autocomplete-target").val(ui.item.value); //ui.item is your object from the array
                return false;
            },
            change: function (event, ui) {
                $(".form-autocomplete").val((ui.item ? ui.item.label : ""));
                $(".form-autocomplete-target").val((ui.item ? ui.item.value : ""));
            }
        };

        $(document).on('keydown.autocomplete', ".form-autocomplete", function () {
            $(this).autocomplete(author_autocomplite_options);
        });

        //post type mapping

        $(document).on('click', '.selbox', function () {

            var idds = $(this).attr('id');
            if (idds == 'single') {
                $('.cl_allboxes').hide();
                $('.single_box').show();
            } else {
                $('.cl_allboxes').hide();
                $('.double_box').show();
            }
        });

        //fields mapping

        //use WP checkboxes
        $(document).on('change', '.js-use-wp', function () {
            var wp_selector = $(this).data('target-wp');
            var cl_selector = $(this).data('target-cl');
            if ($(this).prop('checked')) {
                $('.' + wp_selector).show();
                $('.' + cl_selector).hide();
            } else {
                $('.' + wp_selector).hide();
                $('.' + cl_selector).show();
            }
        });

        //get acf fields
        $(document).on('change', '#acf_load_fields', function () {

            var data = {
                'action': 'get_html_acf_fields',
                'acf_id': $(this).val(),
                'api_key': $('input[name=api_key]').val(),

            };

            $('#loader_div1').show();
            $.ajax({
                url: contently_data.admin_url + '/admin-ajax.php',
                type: 'POST',
                data: data,
                success: function (data) {
                    if (!data == '') {
                        if (data.slice(-1) === '0') {
                            data = data.slice(0, -1);
                        }
                        $('.embed_acf_fields').html(data);
                    }
                    $('#loader_div1').hide();
                },
                error: function (errorThrown) {
                    console.log(errorThrown);
                }
            });
        });

        //contently custom fields

        $(document).on('click', ".js-add-new-field", function () {
            $('.js-new-field').show();
            $('.js-add-field').show();
            $('.js-add-field-description').show();
            $('.js-add-new-field-close').show();
            $(this).hide();
        });

        $(document).on('click', ".js-add-new-field-close", function () {
            $('.js-new-field').hide();
            $('.js-add-field').hide();
            $('.js-add-field-description').hide();
            $('.js-add-new-field').show();
            $(this).hide();
        });

        $(document).on('click', ".js-add-field", function () {
            var new_field = $('.js-new-field').val();
            if (!text_validation(new_field)) {
                $.toast(contently_data.messages.allowed_chars, {'type': 'danger', 'align': 'right-bottom'});
                return false;
            }
            var template = $('.js-custom-field-template').clone();
            template.removeClass('hidden').removeClass('js-custom-field-template');
            template.find('.js-template-select').find('select').attr('name', 'mapping_fields[cl_cf_' + new_field + ']');
            template.find('.js-template-name').html(new_field + '<a class="js-delete-custom-field delete-ico cl-right" > x </a>');
            $('.js-custom-field-template').before(template);
        });

        $(document).on('click', ".js-delete-custom-field", function () {
            var target = $(this).closest('.cl-row');
            target.remove();
        });

        function key_validation(key) {

            if (key.length != 32) {
                $.toast(contently_data.messages.invalid_key, {'type': 'danger', 'align': 'right-bottom'});
                return false;
            }
            if (!text_validation(key)) {
                $.toast(contently_data.messages.allowed_chars, {'type': 'danger', 'align': 'right-bottom'});
                return false;
            }
            return true;
        }

        function text_validation(text) {
            if (text.match(/^[a-z0-9_ ]+$/i) == null) {
                return false;
            }
            return true;
        }

        //end of fields mapping script
    });

})(jQuery);