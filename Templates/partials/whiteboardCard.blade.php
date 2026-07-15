<div class="col-md-3">
    <div class="well" style="text-align: center; padding: 15px; margin-bottom: 15px; border-radius: 12px;">

        <span class="fa fa-chalkboard" style="font-size: 32px; color: #666; display: block; margin-bottom: 10px;"></span>

        {{-- Title (click to rename) --}}
        <h5 class="wb-title" id="wb-title-{{ $wb['id'] }}" style="margin: 0 0 5px 0; cursor: pointer;"
            data-id="{{ $wb['id'] }}" data-title="{{ $tpl->escape($wb['title']) }}"
            title="Click to rename">
            {{ $tpl->escape($wb['title']) }}
            <small class="fa fa-pencil" style="color: #ccc; font-size: 11px;"></small>
        </h5>

        {{-- Inline rename form (hidden by default) --}}
        <div class="wb-rename-form" id="wb-rename-form-{{ $wb['id'] }}" style="display: none; margin-top: 5px;">
            <input type="text" class="form-control input-sm wb-rename-input" id="wb-rename-input-{{ $wb['id'] }}"
                   value="{{ $tpl->escape($wb['title']) }}" style="margin-bottom: 5px; width: 100%;" />
            <button class="btn btn-primary btn-xs wb-rename-save" data-id="{{ $wb['id'] }}">Save</button>
            <button class="btn btn-default btn-xs wb-rename-cancel" data-id="{{ $wb['id'] }}">Cancel</button>
            <span class="wb-rename-status" style="margin-left: 5px; font-size: 11px;"></span>
        </div>

        <small style="color: #999;">{{ $wb['created'] ?? '' }}</small>

        <div style="margin-top: 12px;">
            <a href="{{ BASE_URL }}/whiteboards/showWhiteboard/{{ $wb['id'] }}"
               class="btn btn-primary btn-sm">
                <span class="fa fa-external-link"></span> {!! __('buttons.open_whiteboard') !!}
            </a>
        </div>

        <div style="margin-top: 8px;">
            <a href="javascript:void(0)"
               class="deleteWhiteboard"
               data-id="{{ $wb['id'] }}"
               data-url="{{ BASE_URL }}/whiteboards/delete/{{ $wb['id'] }}"
               style="color: #d9534f;">
                <span class="fa fa-trash"></span> Delete
            </a>
        </div>
    </div>
</div>
