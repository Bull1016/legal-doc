/**
 * Organisation Form Script
 */
'use strict';

(function () {
  // ==========================
  // ✍️ Initialisation Quill
  // ==========================
  document.addEventListener('DOMContentLoaded', function () {
    const toolbar = [
      [
        {
          font: []
        },
        {
          size: []
        }
      ],
      ['bold', 'italic', 'underline', 'strike'],
      [
        {
          color: []
        },
        {
          background: []
        }
      ],
      [
        {
          header: '1'
        },
        {
          header: '2'
        },
        'blockquote'
      ],
      [
        {
          list: 'ordered'
        },
        {
          indent: '-1'
        },
        {
          indent: '+1'
        }
      ],
      ['link', 'image', 'video']
    ];
    const snowEditor = new Quill('#snow-editor', {
      bounds: '#snow-editor',
      placeholder: window.translations.activity_description,
      modules: {
        syntax: true,
        toolbar: toolbar
      },
      theme: 'snow'
    });

    // À chaque changement dans l'éditeur, on met à jour l'input hidden
    snowEditor.on('text-change', function () {
      document.getElementById('description').value = snowEditor.root.innerHTML;
    });

    // 📌 Préreplissage de Quill lors de l'édition
    const descriptionValue = document.getElementById('description').value;
    if (descriptionValue) {
      snowEditor.clipboard.dangerouslyPasteHTML(descriptionValue);
    }
  });

  // ==========================
  // 🧭 Boutons
  // ==========================
  $('#cancelBtn').on('click', function () {
    window.location.href = window.routes.activityIndex;
  });

  $('#resetBtn').on('click', function () {
    $('#activityForm')[0].reset();
    $('#activityForm .is-invalid').removeClass('is-invalid');
    $('#activityForm .invalid-feedback').removeClass('d-block');
    $('#bannerPreview').hide();
    $('#previewImage').attr('src', '');
  });

  // ==========================
  // 🖼️ Aperçu de l'image
  // ==========================
  $('#banner').on('change', function (e) {
    const file = e.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function (e) {
        $('#previewImage').attr('src', e.target.result);
        $('#bannerPreview').show();
        // Masquer l'image actuelle si elle existe
        $('#currentbanner').hide();
      };
      reader.readAsDataURL(file);
    } else {
      $('#bannerPreview').hide();
      $('#previewImage').attr('src', '');
      $('#currentbanner').show();
    }
  });

  // ==========================
  // 🧪 FormValidation.io
  // ==========================
  const form = document.getElementById('activityForm');
  const fv = FormValidation.formValidation(form, {
    fields: {
      name: {
        validators: {
          notEmpty: {
            message: window.translations.activity_name_required
          }
        }
      },
      place: {
        validators: {
          notEmpty: { message: window.translations.activity_place_required }
        }
      },
      banner: {
        validators: {
          notEmpty: {
            enabled: !$('#activityForm').find('[name="activity_id"]').val(),
            message: window.translations.activity_banner_required || "La banière de l'Activité est obligatoire"
          },
          file: {
            extension: 'jpg,jpeg,png,gif,webp',
            type: 'image/jpeg,image/png,image/gif,image/webp',
            maxSize: 2097152, // 2 MB
            message:
              window.translations.org_pic_invalid ||
              'Veuillez sélectionner une image valide (JPG, PNG, GIF, WEBP, max 2MB)'
          }
        }
      },
      description: {
        validators: {
          notEmpty: {
            message: window.translations.activity_description_required
          }
        }
      }
    },
    plugins: {
      trigger: new FormValidation.plugins.Trigger(),
      bootstrap5: new FormValidation.plugins.Bootstrap5({
        rowSelector: '.mb-6',
        eleInvalidClass: 'is-invalid',
        eleValidClass: 'is-valid'
      }),
      submitButton: new FormValidation.plugins.SubmitButton(),
      autoFocus: new FormValidation.plugins.AutoFocus()
    }
  });

  // ==========================
  // 🟦 Soumission AJAX
  // ==========================
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
  fv.on('core.form.valid', function () {
    const originalText = $('#submitBtn').html();
    $('#submitBtn')
      .prop('disabled', true)
      .html('<span class="spinner-border spinner-border-sm me-2"></span>' + window.translations.submitting);

    const activityId = $('#activityForm').find('[name="activity_id"]').val();
    const url = activityId
      ? window.routes.activityUpdate.replace(':id', activityId)
      : window.routes.activityStore;

    // 🧾 Préparer le FormData (inclut fichiers)
    const formData = new FormData($('#activityForm')[0]);
    if (activityId) {
      formData.append('_method', 'PUT');
    }

    $.ajax({
      url: url,
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        notyf.success(response.message);
        window.location.href = window.routes.activityIndex;
      },
      error: function (error) {
        const respJson = error.responseJSON;
        notyf.error(respJson.message);

        // Réinitialiser les erreurs
        $('#activityForm .is-invalid').removeClass('is-invalid');
        $('#activityForm .invalid-feedback').removeClass('d-block');

        if (respJson && respJson.errors) {
          const errors = respJson.errors;
          for (const key in errors) {
            const input = $('#activityForm').find('[name="' + key + '"]');
            input.addClass('is-invalid');
            input.next('.invalid-feedback').addClass('d-block').text(errors[key][0]);
          }
        }
      },
      complete: function () {
        $('#submitBtn').prop('disabled', false).html(originalText);
      }
    });
    setTimeout(function () {
      $('#submitBtn').prop('disabled', false).html(originalText);
    }, 500);
  });

  // Déclencheur de soumission classique
  $('#submitBtn').on('click', function () {
    fv.validate();
  });
})();
