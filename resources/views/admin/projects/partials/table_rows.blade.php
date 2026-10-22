@forelse($projects as $project)
<tr class="text-center" id="project-row-{{ $project->slug }}">              
    <td>
        <input class="form-check-input project-checkbox" type="checkbox" value="{{ $project->slug }}" data-title="{{ $project->title }}">
    </td>
    <td class="text-start">
        <div class="fw-bold">{{ $project->title }}</div>
        @if(isset($isAll) && $isAll)
            <small class="text-muted"><i class="fa fa-user me-1"></i>{{ $project->user?->name ?? 'Unknown' }}</small>
        @endif
    </td>
    <td>{{ $project->created_at->format('d M Y') }}</td>
    <td>
        <span class="badge bg-dark"><i class="fa fa-eye me-1"></i> {{ $project->views ?? 0 }}</span>
    </td>                        
    <td style="width: 170px;">
        <a href="{{ route('project.view' , $project->slug) }}" class="btn btn-sm btn-sm-square btn-outline-info m-1" target="_blank" title="View"><i class="fa fa-eye"></i></a>

        <a href="{{ route('edit.project', $project->slug) }}" class="btn btn-sm btn-sm-square btn-outline-warning m-1" title="Edit"><i class="fa fa-edit"></i></a>
        
        <button type="button" class="btn btn-sm btn-sm-square btn-outline-danger m-1 delete-project-btn" data-slug="{{ $project->slug }}" data-title="{{ $project->title }}" title="Delete"><i class="fa fa-trash"></i></button>
    </td>
</tr>
@empty
<tr class="no-projects-row">
    <td colspan="5" class="text-center text-muted py-4">No projects found.</td>
</tr>
@endforelse
