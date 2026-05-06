@extends('layout.master-layout')
@section('title', 'Terms & Conditions')

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            {{-- Page Title --}}
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                        <h4 class="mb-sm-0">Terms & Conditions</h4>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('show.admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Terms & Conditions</li>
                        </ol>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="ri-shield-check-line me-2 text-primary"></i> Manage Terms & Conditions
                            </h5>
                        </div>
                        <div class="card-body">
                            <form id="termsConditionsForm">
                                <div class="row g-3">
                                    <div class="col-lg-12">
                                        <label for="title" class="form-label">Page Title <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="title" name="title"
                                            value="{{ $page?->title ?? 'Terms & Conditions' }}" placeholder="Enter page title">
                                        <div class="text-danger small mt-1 field-error" id="error-title"></div>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">Content <span class="text-danger">*</span></label>
                                        <div id="contentEditor" class="snow-editor" style="height: 400px;"></div>
                                        <input type="hidden" id="content" name="content" value="{{ $page?->content }}">
                                        <div class="text-danger small mt-1 field-error" id="error-content"></div>
                                    </div>

                                    <div class="col-lg-6">
                                        <label for="meta_title" class="form-label">Meta Title</label>
                                        <input type="text" class="form-control" id="meta_title" name="meta_title"
                                            value="{{ $page?->meta_title }}" placeholder="Meta Title for SEO">
                                    </div>

                                    <div class="col-lg-6">
                                        <label for="meta_description" class="form-label">Meta Description</label>
                                        <textarea class="form-control" id="meta_description" name="meta_description" rows="1" placeholder="Meta Description for SEO">{{ $page?->meta_description }}</textarea>
                                    </div>

                                    <div class="col-12">
                                        <div class="hstack gap-2 justify-content-end">
                                            <button type="submit" class="btn btn-primary" id="saveBtn">
                                                <span class="btn-text"><i class="ri-save-line me-1"></i> Save Changes</span>
                                                <span class="btn-spinner d-none">
                                                    <span class="spinner-border spinner-border-sm me-1"></span> Saving…
                                                </span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const editorEl = document.getElementById('contentEditor');
            const contentInput = document.getElementById('content');
            
            // Ensure editor is initialized
            let editor = Quill.find(editorEl);
            if (!editor && typeof Quill !== 'undefined') {
                editor = new Quill(editorEl, {
                    theme: 'snow'
                });
            }

            // Load existing content
            if (editor && contentInput.value) {
                editor.clipboard.dangerouslyPasteHTML(contentInput.value);
            }

            // Keep hidden input in sync
            if (editor) {
                editor.on('text-change', function() {
                    contentInput.value = editor.getSemanticHTML ? editor.getSemanticHTML() : editor.root.innerHTML;
                });
            }

            document.getElementById('termsConditionsForm').addEventListener('submit', function(e) {
                e.preventDefault();

                // Final sync before submission
                if (editor) {
                    contentInput.value = editor.getSemanticHTML ? editor.getSemanticHTML() : editor.root.innerHTML;
                }

                document.querySelectorAll('.field-error').forEach(el => el.textContent = '');
                document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

                const btn = document.getElementById('saveBtn');
                if (btn.disabled) return; // Prevent double submission

                btn.disabled = true;
                btn.querySelector('.btn-text').classList.add('d-none');
                btn.querySelector('.btn-spinner').classList.remove('d-none');

                axios.post('{{ route('admin.terms-conditions.store') }}', {
                        title: document.getElementById('title').value,
                        content: contentInput.value,
                        meta_title: document.getElementById('meta_title').value,
                        meta_description: document.getElementById('meta_description').value,
                    })
                    .then(res => Toast.success(res.data.message))
                    .catch(function(err) {
                        const data = err.response?.data;
                        if (data?.errors) {
                            Object.entries(data.errors).forEach(function([field, messages]) {
                                const errorEl = document.getElementById('error-' + field);
                                const inputEl = document.getElementById(field);
                                if (errorEl) errorEl.textContent = messages[0];
                                if (inputEl) inputEl.classList.add('is-invalid');
                            });
                            Toast.error(data.message || 'Please fix the errors below.');
                        } else {
                            Toast.fromResponse(data);
                        }
                    })
                    .finally(function() {
                        btn.disabled = false;
                        btn.querySelector('.btn-text').classList.remove('d-none');
                        btn.querySelector('.btn-spinner').classList.add('d-none');
                    });
            });
        });
    </script>
@endpush
