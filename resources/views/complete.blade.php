@extends('partials.layout')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between mb-5">
                    <h1 class="font-bold">Completed List</h1>
                </div>

                <div class="overflow-x-auto">
                    <table class="table table-zebra text-center">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Title</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($notes as $note)
                                <tr class="hover">
                                    <th>{{ $loop->index + 1 }}</th>
                                    <td class="text-start">{{ $note->title }}</td>
                                </tr>
                        </tbody>
                        @endforeach
                    </table>

                    <div class="mt-4 mb-2">
                        {{ $notes->links('vendor.pagination.tailwind') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
