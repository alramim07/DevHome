@forelse($projects as $project)
<tr class="text-center">              
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
        
        <form action="{{ route('projects.destroy', $project->slug) }}" method="POST" style="display:inline;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-sm-square btn-outline-primary m-1" onclick="return confirm('Delete this project?')" title="Delete"><i class="fa fa-trash"></i></button>
        </form>
    </td>
</tr>
@empty
<tr>
    <td colspan="4" class="text-center text-muted py-4">No projects found.</td>
</tr>
@endforelse
