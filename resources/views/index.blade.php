@php
    use Carbon\Carbon;
@endphp

@extends('partials.layout')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between mb-5">
                    <h1 class="font-bold">{{ $title }} List</h1>
                    @if ($title == 'To Do')
                        <button class="btn btn-warning btn-sm" onclick="todo.showModal()">
                            Add To Do
                            <i class="fa-solid fa-plus"></i>
                        </button>
                    @endif
                </div>

                <div class="overflow-x-auto">
                    <table class="table table-zebra text-center">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Title</th>
                                <th>Scheduled At</th>

                                <th>Created At</th>

                                @if ($title == 'Completed')
                                    <th>Completed At</th>
                                @endif

                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($notes as $note)
                                <tr class="hover">
                                    <th>{{ $loop->index + 1 }}</th>
                                    <td class="text-start">{{ $note->title }}</td>
                                    <td>{{ $note->scheduled_at ? Carbon::parse($note->scheduled_at)->translatedFormat('d F Y H:i') : '-' }}
                                    </td>

                                    <td>{{ Carbon::parse($note->created_at)->setTimezone('Asia/Jakarta')->translatedFormat('d F Y H:i') }}
                                    </td>

                                    @if ($title == 'Completed')
                                        <td>{{ Carbon::parse($note->completed_at)->setTimezone('Asia/Jakarta')->translatedFormat('d F Y H:i') }}
                                        </td>
                                    @endif

                                    <td>
                                        <div class="badge badge-{{ $note->is_done ? 'success' : 'error' }}">
                                            {{ $note->is_done ? 'Completed' : 'Uncompleted' }}
                                        </div>
                                    </td>

                                    <td>
                                        <div class="flex justify-center items-center space-x-2">
                                            <i class="fas fa-eye text-black"
                                                onclick='viewData(@json($note), "view")'></i>
                                            <i class="fas fa-edit text-black"
                                                onclick='viewData(@json($note), "edit")'></i>
                                            <i class="fas fa-trash text-black"
                                                onclick="deleteData({{ $note->id }})"></i>
                                            @if ($title == 'To Do')
                                                <button onclick="doneData({{ $note->id }})"
                                                    class="btn btn-xs btn-success">Done</button>
                                            @else
                                                <button onclick="undoneData({{ $note->id }})"
                                                    class="btn btn-xs btn-error">Undone</button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="mt-4 mb-2">
                        {{ $notes->links('vendor.pagination.tailwind') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <dialog id="todo" class="modal">
        <div class="modal-box w-full max-w-2xl">
            <input type="hidden" id="id" />
            <h3 class="text-lg font-bold mb-4">To Do</h3>

            <label class="form-control w-full mb-2">
                <div class="label">
                    <span class="label-text">Title <span class="text-red-500">*</span></span>
                </div>
                <input type="text" placeholder="Insert title" class="input input-bordered w-full input-md"
                    id="title" />
                <span id="title_error" class="text-red-500 text-sm error-message"></span>
            </label>

            <label class="form-control mb-2">
                <div class="label">
                    <span class="label-text">Content <span class="text-red-500">*</span></span>
                </div>
                <textarea id="content" class="textarea textarea-bordered h-20" placeholder="Insert content"></textarea>
                <span id="content_error" class="text-red-500 text-sm error-message"></span>
            </label>

            <label class="form-control w-full mb-2">
                <div class="label">
                    <span class="label-text">Scheduled At</span>
                </div>
                <input type="datetime-local" class="input input-bordered w-full input-md" id="scheduled_at" />
                <span id="scheduled_at_error" class="text-red-500 text-sm error-message"></span>
            </label>

            <label class="form-control w-full">
                <div class="label">
                    <span class="label-text">Attachment</span>
                </div>
                <input id="attachment" type="file"
                    class="file-input file-input-ghost file-input-bordered w-full file-input-md" />
                <a id="attachment_link" class="link" target="_blank"></a>
                <span id="attachment_error" class="text-red-500 text-sm error-message"></span>
            </label>

            <div class="modal-action">
                <div class="flex space-x-2 justify-end">
                    <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2" onclick="resetModal()">✕</button>
                    <button class="btn btn-error btn-sm" onclick="resetModal()">Close</button>
                    <button class="btn btn-warning btn-sm" onclick="saveData()" id="btn_save">Save</button>
                    <button class="btn btn-warning btn-sm hidden" onclick="updateData()" id="btn_update">Update</button>
                </div>
            </div>
        </div>
    </dialog>
@endsection

@section('js')
    <script>
        function resetModal() {
            $('#todo').get(0).close();
            $('input').val('')
            $('textarea').val('')

            $('.error-message').text('');
            $('#attachment_link').text('');
            $('.input, .textarea').removeClass('border-red-500');

            $('input').prop('disabled', false);
            $('textarea').prop('disabled', false);
            $('#btn_save').show();
            $('#btn_update').hide();
        }

        function validateData() {
            var contraints = {
                title: {
                    presence: true,
                    length: {
                        minimum: 1,
                        message: "can't be blank"
                    },
                },
                content: {
                    presence: true,
                    length: {
                        minimum: 1,
                        message: "can't be blank"
                    },
                },
            }

            var title = $('#title').val()
            var content = $('#content').val()
            var scheduled_at = $('#scheduled_at').val()
            var attachment = $('#attachment')[0]

            $('.error-message').text('');
            $('.input, .textarea').removeClass('border-red-500');

            const errors = validate({
                title: title,
                content: content,
            }, contraints);

            if (errors) {
                for (const field in errors) {
                    if (errors.hasOwnProperty(field)) {
                        $(`#${field}_error`).text(errors[field][0]);
                        $(`#${field}`).addClass('border-red-500');
                    }
                }
                return;
            }


            var formData = new FormData();

            var file = attachment.files.length > 0 ? attachment.files[0] : null;
            if (file) {
                if (file.size > 2 * 1024 * 1024) {
                    $('#attachment_error').text('Maximal size 2MB');
                    return;
                }

                formData.append('attachment', file);
            }

            if (scheduled_at) {
                if (scheduled_at < new Date().toISOString().split('T')[0]) {
                    $('#scheduled_at_error').text('Date must be in the future');
                    return;
                }

                formData.append('scheduled_at', scheduled_at);
            }

            formData.append('_token', '{{ csrf_token() }}');
            formData.append('title', title);
            formData.append('content', content);

            return formData
        }

        function saveData() {
            var data = validateData()

            if (!data) {
                return
            }

            $('#todo').get(0).close();
            $.ajax({
                url: '/',
                type: 'POST',
                data: data,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Add Success',
                        text: response.message,
                        confirmButtonText: 'OK'
                    }).then(() => {
                        resetModal();
                        location.reload();
                    });
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Server Error',
                        text: 'Try Again',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        $('#todo').get(0).showModal();
                    })
                }
            });
        }

        function viewData(note, action) {
            $('#title').val(note.title)
            $('#content').val(note.content)
            $('#scheduled_at').val(note.scheduled_at ? note.scheduled_at : '')
            $('#btn_save').hide();

            if (note.attachment) {
                $('#attachment_link').text(note.attachment_name);
                $('#attachment_link').attr('href', note.attachment ? '/storage/' + note.attachment : '');
            }

            if (action == 'view') {
                $('input').prop('disabled', true);
                $('textarea').prop('disabled', true);
            } else if (action == 'edit') {
                $('#btn_update').show();
                $('#id').val(note.id);
            }

            $('#todo').get(0).showModal();
        }

        function updateData() {
            var data = validateData()

            if (!data) {
                return
            }

            var id = $('#id').val();

            $('#todo').get(0).close();
            $.ajax({
                url: `/${id}`,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content'),
                    '_method': 'PATCH'
                },
                data: data,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Update Success',
                        text: response.message,
                        confirmButtonText: 'OK'
                    }).then(() => {
                        resetModal();
                        location.reload();
                    });
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Server Error',
                        message: 'Try Again',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        $('#todo').get(0).showModal();
                    })
                }
            });
        }

        function deleteData(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/' + id,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Delete Success',
                                text: response.message,
                                confirmButtonText: 'OK'
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Server Error',
                                text: 'Try Again',
                                confirmButtonText: 'OK'
                            })
                        }
                    });
                }
            });
        }

        function doneData(id) {
            Swal.fire({
                title: 'Are you sure to done it?',
                text: "You still able to revert this",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, done!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/done/' + id,
                        type: 'PUT',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Done Success',
                                text: response.message,
                                confirmButtonText: 'OK'
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Server Error',
                                text: 'Try Again',
                                confirmButtonText: 'OK'
                            })
                        }
                    });
                }
            });
        }

        function undoneData(id) {
            Swal.fire({
                title: 'Are you sure to undone it?',
                text: "You still able to revert this",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, undone!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/undone/' + id,
                        type: 'PUT',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Done Success',
                                text: response.message,
                                confirmButtonText: 'OK'
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Server Error',
                                text: 'Try Again',
                                confirmButtonText: 'OK'
                            })
                        }
                    });
                }
            });
        }
    </script>
@endsection
