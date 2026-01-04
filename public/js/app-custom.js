
// ================================
// CSRF setup
// ================================
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

// ================================
// Bootstrap dropdowns
// ================================
document.addEventListener("DOMContentLoaded", function () {
    var dropdowns = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
    dropdowns.map(function (el) {
        return new bootstrap.Dropdown(el);
    });
});

// ================================
// Favorites system
// ================================
$(document).on('submit', '.favorite-form', function (e) {
    e.preventDefault();

    var form = $(this);
    var url = form.attr('action');
    var button = form.find('button[type="submit"]');
    var icon = button.find('i');
    var buttonTextSpan = button.find('#favorite-button-text');

    $.ajax({
        type: 'POST',
        url: url,
        data: form.serialize(),
        success: function (response) {
            toastr.success(response.message);

            if (response.status === 'favorited') {
                icon.removeClass('far fa-heart').addClass('fas fa-heart');
                button.removeClass('btn-outline-danger').addClass('btn-danger');

                if (buttonTextSpan.length) {
                    buttonTextSpan.text(@json(__('Retirer des favoris')));
                } else {
                    button.html(
                        '<i class="fas fa-heart me-2"></i> ' +
                        @json(__('Retirer des favoris'))
                    );
                }
            } else {
                icon.removeClass('fas fa-heart').addClass('far fa-heart');
                button.removeClass('btn-danger').addClass('btn-outline-danger');

                if (buttonTextSpan.length) {
                    buttonTextSpan.text(@json(__('Ajouter aux favoris')));
                } else {
                    button.html(
                        '<i class="far fa-heart me-2"></i> ' +
                        @json(__('Ajouter aux favoris'))
                    );
                }

                if (form.closest('.book-card-col').length) {
                    form.closest('.book-card-col').fadeOut();
                }
            }
        },
        error: function () {
            toastr.error(@json(__('An error occurred. Please try again.')));
        }
    });
});


// ================================
// Notifications (AUTH ONLY)
// ================================
@auth
function fetchNotifications() {
    $.ajax({
        url: @json(route('api.notifications.index')),
        method: 'GET',
        success: function (response) {
            let countEl = $('#unread-notifications-count');
            countEl.text(response.unread_count);

            response.unread_count > 0 ? countEl.show() : countEl.hide();

            let notificationsList = $('#notifications-list');
            notificationsList.empty();

            if (response.notifications.length > 0) {
                response.notifications.forEach(function (notification) {
                    notificationsList.append(`
                        <a class="dropdown-item notification-item"
                           href="${notification.link ?? '#'}"
                           data-id="${notification.id}">
                            <strong>${notification.title}</strong><br>
                            <small>${notification.message}</small>
                        </a>
                    `);
                });

                notificationsList.append('<div class="dropdown-divider"></div>');
                notificationsList.append(
                    '<a class="dropdown-item text-center" href="#">' +
                    @json(__('View all notifications')) +
                    '</a>'
                );
            } else {
                notificationsList.append(
                    '<span class="dropdown-item">' +
                    @json(__('No new notifications.')) +
                    '</span>'
                );
            }
        },
        error: function (xhr) {
            console.error('Error fetching notifications:', xhr);
        }
    });
}

// Initial load
fetchNotifications();

// Refresh every 60s
setInterval(fetchNotifications, 60000);

// Mark as read
$(document).on('click', '.notification-item', function (e) {
    e.preventDefault();

    let notificationId = $(this).data('id');
    let notificationLink = $(this).attr('href');
    let $this = $(this);

    $.ajax({
        url: `/api/notifications/${notificationId}/mark-as-read`,
        method: 'POST',
        data: {
            _token: @json(csrf_token())
        },
        success: function () {
            $this.removeClass('bg-light');
            fetchNotifications();

            if (notificationLink && notificationLink !== '#') {
                window.location.href = notificationLink;
            }
        },
        error: function (xhr) {
            console.error('Error marking notification as read:', xhr);
        }
    });
});
@endauth

