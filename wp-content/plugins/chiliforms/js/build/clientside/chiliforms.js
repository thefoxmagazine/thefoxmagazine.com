(function ($) {
  "use strict";

  /**
   * Converts jQuery collection to native array
   * @param $jq
   * @private
   */
  function _toArray($jq) {
    return Array.prototype.map.call($jq, function (item) {
      return item;
    });
  }

  /**
   * Wrapper that adds nonce prop to request object
   * @param data
   * @returns {*}
   */
  function addAjaxNonce(data) {
    data[window.KCFData.nonce.nonceKey] = window.KCFData.nonce.nonce;

    return data;
  }

  /**
   * Traverses all controls and gets values
   * @param $form
   * @returns {{}}
   */
  function getFormValues($form) {
    return _toArray($form.find('.cf-field-wrap')).reduce(function (formValues, field) {
      var $field = $(field);
      var fieldType = field.dataset.type;
      var fieldId = field.dataset.id;
      var $control = $field.find('.cf-form-control');

      formValues[$control.attr('name')] = {
        id: fieldId,
        value: getFieldValue($control, fieldType)
      };

      return formValues;
    }, {});
  }

  /**
   * Gets field type by data attribute
   * @param $field
   * @returns {*}
   */
  function getFieldType($field) {
    return $field.data('type');
  }

  /**
   * Checks given field for empty value
   * @param value
   * @param fieldType
   * @returns {boolean}
   */
  function isEmptyValue(value, fieldType) {
    if (!value) {
      return true;
    }

    switch (fieldType) {
      case 'checkbox':
        // check for all false values in options array
        return value.filter(function (option) {
            return option.value === true;
          }).length === 0;
        break;

      case 'radio':
      case 'dropdown':
        // check for both empty label and key
        return value.key === '' && value.label === '';
        break;

      default:
        break;
    }

    return false;
  }

  /**
   * Removes all validation messages
   * @param $form
   */
  function clearValidation($form) {
    $form.find('.cf-field-wrap').each(function (index, field) {
      $(field).removeClass('cf-validation-error cf-validation-success').find('cf-field-validation-message').html('');
    });
  }

  /**
   * Adds server errors to fields
   * @param $form
   * @param response
   */
  function validateFieldsByServerResponse($form, response) {
    if (response.errors && response.errors['fields']) {
      clearValidation($form);

      response.errors['fields'].forEach(function (field) {
        var id = field['id'];
        var $field = $form.find('.cf-field-wrap[data-id="' + id + '"]');

        $field.removeClass('cf-validation-success');
        $field.addClass('cf-validation-error');
        $field.find('.cf-field-validation-message').html('Server validation: ' + field['error_message']);
      });
    }
  }

  /**
   * Messages animation
   * @param $messages
   */
  function animateMessages($messages) {
    $messages.css('opacity', 1);
  }

  /**
   * Shows any global errors received from server
   * @param $form
   * @param response
   */
  function showGlobalErrors($form, response) {
    var $messages = $form.find('.cf-form-messages');

    if (response.errors && response.errors['global']) {
      response.errors['global'].forEach(function (error) {
        $messages.append('<p>Error ' + error.code + ': ' + error.error_message + '</p>');
      });

      animateMessages($messages);
    }
  }

  /**
   * Shows reCAPTCHA related errors
   * @param $form
   * @param response
   */
  function showRecaptchaErrors($form, response) {
    var $messages = $form.find('.cf-form-messages');

    if (response.errors && response.errors['recaptcha']) {
      response.errors['recaptcha'].forEach(function (error) {
        $messages.append('<p>reCAPTCHA error: ' + error.error_message + '</p>');
      });

      animateMessages($messages);
    }
  }

  /**
   * Fields validation map
   */
  var validationMap = [
    {
      id: 'required',
      filter: '.cf-required-field',
      validator: validateRequiredValue,
      message: 'This field is required'
    },
    {
      id: 'email',
      filter: '.cf-field-type-email',
      validator: validateEmailValue,
      message: 'Please, enter valid email address'
    },
    {
      id: 'url',
      filter: '.cf-field-type-url',
      validator: validateUrlValue,
      message: 'Please, enter valid URL'
    },
    {
      id: 'alpha',
      filter: '[data-validation-type="alpha"]',
      validator: validateAlphaValue,
      message: 'Only characters allowed'
    },
    {
      id: 'number',
      filter: '[data-validation-type="num"]',
      validator: validateNumberValue,
      message: 'Only numbers allowed'
    },
    {
      id: 'alphaNumber',
      filter: '[data-validation-type="alphanum"]',
      validator: validateAlphaNumberValue,
      message: 'Only numbers and characters allowed'
    }
  ];

  /**
   * Validates required field
   * @param value
   * @param fieldType
   * @param field
   * @returns {boolean}
   */
  function validateRequiredValue(value, fieldType, field) {
    return !isEmptyValue(value, fieldType);
  }
  /**
   * Validators
   */

  /**
   * Validates email
   * @param value
   * @param fieldType
   * @param field
   * @returns {boolean}
   */
  function validateEmailValue(value, fieldType, field) {
    var emailTest = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

    return !(value.trim() !== '' && !emailTest.test(value));
  }

  /**
   * Validates url
   * @param value
   * @param fieldType
   * @param field
   * @returns {boolean}
   */
  function validateUrlValue(value, fieldType, field) {
    var patternWithProtocol = new RegExp('(http|ftp|https)://[\w-]+(\.[\w-]+)+([\w.,@?^=%&amp;:/~+#-]*[\w@?^=%&amp;/~+#-])?', 'i');
    var patternSimple = new RegExp('^(http(s)?(:\\/\\/))?(www\\.)?[a-zA-Z0-9-_\\.]+(\\.[a-zA-Z0-9]{2,})([-a-zA-Z0-9:%_\\+.~#?&\\/\\/=]*)', 'i');

    return !(value.trim() !== '' && !patternSimple.test(value));
  }

  /**
   * Validates alphanumeric values
   * @param value
   * @param fieldType
   * @param field
   * @returns {boolean}
   */
  function validateAlphaValue(value, fieldType, field) {
    var pattern = new RegExp('^[a-zA-Z]*$', 'i');

    return pattern.test(value);
  }

  /**
   * Validates number values
   * @param value
   * @param fieldType
   * @param field
   * @returns {boolean}
   */
  function validateNumberValue(value, fieldType, field) {
    var pattern = new RegExp('^[0-9]*$', 'i');

    return pattern.test(value);
  }

  /**
   * Validates alpha and number values
   * @param value
   * @param fieldType
   * @param field
   * @returns {boolean}
   */
  function validateAlphaNumberValue(value, fieldType, field) {
    var pattern = new RegExp('^[a-zA-Z0-9]*$', 'i');

    return pattern.test(value);
  }

  /**
   * Validate all fields on form
   * @param $form
   * @returns {boolean}
   */
  function validateFields($form) {
    var $fields = $form.find('.cf-field-wrap');
    var values = getFormValues($form);

    clearValidation($form);

    return validationMap.reduce(function(formValid, validationGroup) {
      var $matchingFields = $fields.filter(validationGroup.filter);

      if (!$matchingFields.length) {
        return formValid;
      }

      return _toArray($matchingFields).reduce(function(formValid, field) {
        var $field = $(field);

        // show only one validation error at a time
        if ($field.hasClass('cf-validation-error')) {
          return formValid;
        }

        var $control = $field.find('.cf-form-control');
        var name = $control.attr('name');
        var value = values[name].value;
        var fieldType = getFieldType($field);
        var fieldValidationResult = validationGroup.validator(value, fieldType, field);

        displayFieldValidationResult(fieldValidationResult, $field, validationGroup.message);

        return formValid && fieldValidationResult;
      }, formValid);
    }, true);
  }

  /**
   * Updates field with validation info
   * @param result
   * @param $field
   * @param message
   */
  function displayFieldValidationResult(result, $field, message) {
    $field.toggleClass('cf-validation-success', result);
    $field.toggleClass('cf-validation-error', !result);

    if (!result) {
      $field.find('.cf-field-validation-message').html(message);
    }
  }

  /**
   * Gets field value for each control type
   * @param $control
   * @param fieldType
   * @returns {string}
   */
  function getFieldValue($control, fieldType) {
    var value = '';

    switch (fieldType) {
      case 'single-line-input':
      case 'multi-line-input':
      case 'email':
      case 'url':
        value = $control.val();
        break;

      case 'dropdown':
        value = {
          key: $control.val(),
          label: $control.find('option:selected').data('label')
        };
        break;

      case 'radio':
        var $selected = $control.filter(':checked');

        value = {
          key: $selected.val() || '',
          label: $selected.data('label') || ''
        };

        break;

      case 'checkbox':
        value = [];

        $control.each(function (index, control) {
          value.push({
            key: control.value,
            label: control.dataset.label,
            value: !!$(control).attr('checked')
          });
        });

        break;

      default:
        break;
    }

    return value;
  }

  /**
   * finds all chili forms and sets them up
   */
  function init() {
    $('.cf-form').each(function (index, form) {
      var $form = $(form);

      setSubmitHandler($form);
      setDropdownHandlers($form);
    });
  }

  /**
   * Binds submit click handler
   * @param $form
   */
  function setSubmitHandler($form) {
    var formId = parseInt($form.data('formId'), 10);

    $form.on('click', '.cf-submit', function (e) {
      e.preventDefault();

      var $button = $(e.currentTarget);
      var submitText = $button.html();

      if ($button.hasClass('cf-disabled')) {
        return;
      }

      if (!validateFields($form)) {
        return;
      }

      $button.addClass('cf-disabled');
      $button.html('Sending...');

      jQuery.ajax({
          method: 'POST',
          url: window.KCFData.ajaxUrl,
          dataType: 'json',
          data: addAjaxNonce({
            action: 'kcf_submit_form',
            formId: formId,
            formData: {
              values: getFormValues($form),
              recaptchaResult: getRecaptchaResult($form)
            }
          })
        })
        .then(onSubmitResponseReceived.bind(this, $form, $button, submitText))
        .fail(onSubmitConnectionFail.bind(this, $form, $button, submitText));
    });
  }

  /**
   * Gets reCAPTCHA user response
   * @param $form
   * @returns {null}
   */
  function getRecaptchaResult($form) {
    var $recaptchaResult = $form.find('#g-recaptcha-response');
    return $recaptchaResult.length ? $recaptchaResult.val() : null;
  }

  /**
   * Successfully received response from the server, parse it
   * @param $form
   * @param $button
   * @param submitText
   * @param response
   */
  function onSubmitResponseReceived($form, $button, submitText, response) {
    var $messages = $form.find('.cf-form-messages');

    resetSubmitButton($button, submitText);

    if (response.status === 1) {
      handleResponseErrors($form, response);
      return;
    }

    onSuccessSubmit($form, $messages);
  }

  /**
   * Displays error messages to user
   * @param $form
   * @param response
   */
  function handleResponseErrors($form, response) {
    validateFieldsByServerResponse($form, response);
    showGlobalErrors($form, response);
    showRecaptchaErrors($form, response);
  }

  /**
   * Animates form on success
   * @param $form
   * @param $messages
   */
  function onSuccessSubmit($form, $messages) {
    $form.css('min-height', $form.height() + 'px');

    $messages.html('Your submission was sent, thank you!');

    $form.addClass('cf-form-submitted');

    $('html, body').animate({
      scrollTop: $form.offset().top - 100
    }, 200, function () {
      $form.animate({
        'min-height': 0
      }, 400, function () {
        animateMessages($messages);
      });
    });
  }

  function onSubmitConnectionFail($form, $button, submitText) {
    var $messages = $form.find('.cf-form-messages');

    resetSubmitButton($button, submitText);

    $messages.html('Unexpected error on server');
    animateMessages($messages);
  }

  /**
   * Resets submit button to default state
   * @param $button
   * @param submitText
   */
  function resetSubmitButton($button, submitText) {
    $button.html(submitText);
    $button.removeClass('cf-disabled');
  }

  /**
   * Handles styled dropdowns update
   * @param $form
   */
  function setDropdownHandlers($form) {
    $form.on('click', '.cf-wrapped-select', function (e) {
      e.preventDefault();

      $(e.currentTarget).addClass('cf-selected');
    });

    $form.on('click', '.cf-wrapped-select__option', function (e) {
      var $item = $(e.currentTarget);
      var $select = $item.parents('.cf-wrapped-select');
      var $wrap = $item.parents('.cf-field-wrap');
      var $control = $wrap.find('select.cf-form-control');
      var $currentLabel = $select.find('.cf-wrapped-select__current');

      e.preventDefault();
      e.stopPropagation();

      $control.val($item.data('value')).trigger('change');
      $currentLabel.html($item.html());

      $select.removeClass('cf-selected');
    });
  }

  /**
   * ChiliForms bootstrap
   */
  $(document).ready(function () {
    init();
  });
}(jQuery));