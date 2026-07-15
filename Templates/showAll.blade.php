@extends($layout)

@section('content')

<div class="pageheader">
    <div class="pageicon"><span class="fa fa-chalkboard"></span></div>
    <div class="pagetitle">
        <h5>{!! __('headlines.whiteboards') !!}</h5>
        <h1>{!! __('headlines.whiteboards') !!}</h1>
    </div>
</div>

<div class="maincontent">
    <div class="maincontentinner">

        {!! $tpl->displayNotification() !!}

        @if (empty($whiteboards))
            <div class="alert alert-info">
                {!! __('text.no_whiteboards') !!}
            </div>
        @else
            <div class="row">
                @foreach ($whiteboards as $wb)
                    @include('whiteboards::partials.whiteboardCard', ['wb' => $wb])
                @endforeach
            </div>
        @endif

        <div class="row" style="margin-top: 20px;">
            <div class="col-md-12">
                <h4 class="widgettitle title-light">
                    <span class="fa fa-plus"></span> {!! __('buttons.create_whiteboard') !!}
                </h4>
                <form method="post" action="{{ BASE_URL }}/whiteboards/create" class="form-inline">
                    <input type="text" name="title" placeholder="{!! __('label.whiteboard_title') !!}" required
                           style="width: 300px; margin-right: 10px;" class="form-control" />
                    <button type="submit" class="btn btn-primary">{!! __('buttons.create_whiteboard') !!}</button>
                </form>
            </div>
        </div>

    </div>
</div>

@once @push('scripts')
<script>
jQuery(document).ready(function() {
    // Delete confirmation
    jQuery(".deleteWhiteboard").on("click", function (e) {
        if (!confirm("{!! __('confirm.delete_whiteboard') !!}")) {
            e.preventDefault();
        }
    });

    // Inline rename: click title to show form
    jQuery(".wb-title").on("click", function() {
        var id = jQuery(this).data("id");
        jQuery(this).hide();
        jQuery("#wb-rename-form-" + id).show();
        jQuery("#wb-rename-input-" + id).focus().select();
    });

    // Cancel rename
    jQuery(".wb-rename-cancel").on("click", function() {
        var id = jQuery(this).data("id");
        jQuery("#wb-rename-form-" + id).hide();
        jQuery("#wb-title-" + id).show();
    });

    // Save rename
    jQuery(".wb-rename-save").on("click", function() {
        var id = jQuery(this).data("id");
        var newTitle = jQuery("#wb-rename-input-" + id).val().trim();
        if (!newTitle) return;

        var statusEl = jQuery(this).siblings(".wb-rename-status");
        statusEl.text("Saving...");

        jQuery.post(
            leantime.appUrl + "/whiteboards/rename/" + id,
            { title: newTitle },
            function() {
                jQuery("#wb-title-" + id).text(newTitle).data("title", newTitle).show();
                jQuery("#wb-rename-form-" + id).hide();
            }
        ).fail(function() {
            statusEl.text("Failed!");
        });
    });
});
</script>
@endpush @endonce

@endsection
