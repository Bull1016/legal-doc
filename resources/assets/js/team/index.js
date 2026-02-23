/**
 * 🟦 Gestion des equipes mandat
 */

'use strict';

let fv, offCanvasEl, teamdt, currentMandateId; // dt = DataTable instance

document.addEventListener('DOMContentLoaded', function () {
  const offCanvasElementTeam = document.querySelector('#manage-team');
  const offCanvasTitleTeam = offCanvasElementTeam.querySelector('.offcanvas-team-title');
  const offCanvasTitleTeamMandate = offCanvasElementTeam.querySelector('.offcanvas-team-title-mandate');

  const formAddNewTeam = document.getElementById('teamForm');
  const offCanvasElementTeamForm = document.querySelector('#add-new-record-team-form');
  const offCanvasTitleMandateForm = offCanvasElementTeamForm.querySelector('.offcanvas-title-team-form');

  $(document).on('click', '.manage-btn', function () {
    const id = $(this).data('id');
    const url = $(this).data('url');
    const name = $(this).data('name');

    currentMandateId = id;
    $('.create-new-team').data('exercice-id', id);

    offCanvasTitleTeam.textContent = window.translations.mandate_team;
    offCanvasTitleTeamMandate.textContent = name;

    offCanvasEl = new bootstrap.Offcanvas(offCanvasElementTeam);
    offCanvasEl.show();

    // 🔴 IMPORTANT: Détruire le DataTable existant avant de le réinitialiser
    if (teamdt) {
      teamdt.destroy();
    }

    teamdt = $('.datatables-team').DataTable({
      processing: true,
      serverSide: true,
      responsive: true,
      ajax: url,
      columns: [
        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
        { data: 'position', name: 'position' },
        { data: 'member', name: 'member' },
        { data: 'created_at', name: 'created_at' },
        { data: 'actions', name: 'actions', orderable: false, searchable: false }
      ],
      order: [[3, 'desc']]
    });
  });

  // ==========================
  // 🧪 FormValidation.io
  // ==========================
  const fv = FormValidation.formValidation(formAddNewTeam, {
    fields: {
      role_id: {
        validators: {
          notEmpty: {
            message: window.translations.position_required || 'Position is required'
          }
        }
      },
      member_id: {
        validators: {
          notEmpty: {
            message: window.translations.member_required || 'Member is required'
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

  // Function to load roles and members
  function loadRolesAndMembers(mandateId, selectedRoleId = null, selectedMemberId = null) {
    const rolesUrl = `/mandates/${mandateId}/team/roles`;
    const membersUrl = `/mandates/${mandateId}/team/members`;

    // Load roles
    $.ajax({
      url: rolesUrl,
      type: 'GET',
      success: function (response) {
        const roleSelect = $('#role_id');

        // Destroy existing Select2 if it exists
        if (roleSelect.hasClass('select2-hidden-accessible')) {
          roleSelect.select2('destroy');
        }

        roleSelect.empty();
        roleSelect.append(
          '<option value="" selected disabled>' + (window.translations.select || 'Select') + '</option>'
        );

        response.data.forEach(function (role) {
          const option = $('<option></option>').val(role.id).text(role.name);

          if (role.disabled && role.id !== selectedRoleId) {
            option.prop('disabled', true);
          }

          if (selectedRoleId && role.id == selectedRoleId) {
            option.prop('selected', true);
          }

          roleSelect.append(option);
        });

        // Reinitialize Select2
        roleSelect.select2({
          dropdownParent: $('#add-new-record-team-form'),
          placeholder: window.translations.select || 'Select'
        });
      },
      error: function (error) {
        notyf.error(window.translations.failed_to_load_roles || 'Failed to load roles');
      }
    });

    // Load members
    $.ajax({
      url: membersUrl,
      type: 'GET',
      success: function (response) {
        const memberSelect = $('#member_id');

        // Destroy existing Select2 if it exists
        if (memberSelect.hasClass('select2-hidden-accessible')) {
          memberSelect.select2('destroy');
        }

        memberSelect.empty();
        memberSelect.append(
          '<option value="" selected disabled>' + (window.translations.select || 'Select') + '</option>'
        );

        response.data.forEach(function (member) {
          const option = $('<option></option>').val(member.id).text(member.name);

          if (member.disabled && member.id !== selectedMemberId) {
            option.prop('disabled', true);
          }

          if (selectedMemberId && member.id == selectedMemberId) {
            option.prop('selected', true);
          }

          memberSelect.append(option);
        });

        // Reinitialize Select2
        memberSelect.select2({
          dropdownParent: $('#add-new-record-team-form'),
          placeholder: window.translations.select || 'Select'
        });
      },
      error: function (error) {
        notyf.error(window.translations.failed_to_load_members || 'Failed to load members');
      }
    });
  }

  // 🟢 Ouverture du Offcanvas pour création
  document.querySelector('.create-new-team').addEventListener('click', function () {
    offCanvasTitleMandateForm.textContent = window.translations.add;
    formAddNewTeam.reset();
    fv.resetForm(true);
    offCanvasEl = new bootstrap.Offcanvas(offCanvasElementTeamForm);
    offCanvasEl.show();

    const exerciceId = $(this).data('exercice-id');
    $('#exercice_id').val(exerciceId);
    $('#team_id').val(0);

    // Load roles and members
    loadRolesAndMembers(exerciceId);
  });

  // 🟢 Ouverture du Offcanvas pour édition
  $(document).on('click', '.edit-region-btn', function () {
    const currentEditId = $(this).data('id');
    const roleId = $(this).data('role');
    const memberId = $(this).data('member');
    const exerciceId = $(this).data('exercice');

    $('#team_id').val(currentEditId);
    $('#exercice_id').val(exerciceId);

    offCanvasTitleMandateForm.textContent = window.translations.edit || 'Edit';

    offCanvasEl = new bootstrap.Offcanvas(offCanvasElementTeamForm);
    offCanvasEl.show();

    // Load roles and members with selected values
    loadRolesAndMembers(currentMandateId, roleId, memberId);
  });

  // ==========================
  // 🧭 Boutons
  // ==========================
  $('#cancelBtnRegion').on('click', function () {
    offCanvasEl.hide();
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
    const originalText = $('#submitBtnRegion').html();
    $('#submitBtnRegion')
      .prop('disabled', true)
      .html('<span class="spinner-border spinner-border-sm me-2"></span>' + window.translations.submitting);

    const teamId = $('#team_id').val();
    const exerciceId = $('#exercice_id').val();
    const updating = teamId && teamId != 0 ? true : false;
    const url = updating
      ? `/mandates/${currentMandateId}/team/${teamId}/update`
      : `/mandates/${currentMandateId}/team/store`;
    const method = updating ? 'PUT' : 'POST';

    $.ajax({
      url: url,
      type: method,
      data: $(formAddNewTeam).serialize(),
      success: function (response) {
        notyf.success(response.message);
        offCanvasEl.hide();
        teamdt.ajax.reload();
        $('.datatables-basic').DataTable().ajax.reload();
      },
      error: function (error) {
        const respJson = error.responseJSON;
        notyf.error(respJson.message);

        // Réinitialiser les erreurs
        $('#teamForm .is-invalid').removeClass('is-invalid');
        $('#teamForm .invalid-feedback').removeClass('d-block');

        if (respJson && respJson.errors) {
          const errors = respJson.errors;
          for (const key in errors) {
            const input = $('#teamForm').find('[name="' + key + '"]');
            input.addClass('is-invalid');
            input.next('.invalid-feedback').addClass('d-block').text(errors[key][0]);
          }
        }
      },
      complete: function () {
        $('#submitBtnRegion').prop('disabled', false).html(originalText);
      }
    });
  });

  // ==========================
  // 🟦 Suppression AJAX
  // ==========================
  $(document).on('click', '.btn-region-delete', function () {
    const id = $(this).data('id');
    const url = `/mandates/${currentMandateId}/team/${id}/destroy`;

    Swal.fire({
      title: window.translations.team_delete_title || 'Delete team member?',
      text: window.translations.team_delete_text || 'Are you sure you want to delete this team member?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Oui, supprimer',
      cancelButtonText: 'Annuler',
      showClass: {
        popup: 'animate__animated animate__shakeX'
      }
    }).then(result => {
      if (result.isConfirmed) {
        $.ajax({
          url: url,
          type: 'DELETE',
          data: {
            _token: $('meta[name="csrf-token"]').attr('content')
          },
          success: function (response) {
            Swal.fire('Supprimé !', response.message || 'Team member deleted successfully.', 'success');
            $('.datatables-team').DataTable().ajax.reload();
          },
          error: function (xhr) {
            Swal.fire('Erreur', xhr.responseJSON?.message || 'Une erreur est survenue.', 'error');
          }
        });
      }
    });
  });
});
