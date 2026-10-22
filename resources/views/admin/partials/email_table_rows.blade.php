@forelse ($emails as $email)
<tr>
  <th scope="row">{{ $loop->iteration }}</th>
  <td>
    <div class="fw-bold">{{ $email->first_name }} {{ $email->last_name }}</div>
    @if(isset($isAll) && $isAll)
      <small class="text-muted"><i class="fa fa-user me-1"></i>To: {{ $email->user?->name ?? 'Unknown' }}</small>
    @endif
  </td>
  <td><a href="mailto:{{ $email->email }}" class="text-info">{{ $email->email }}</a></td>
  <td>{{ $email->message }}</td>
  <td>{{ $email->created_at->diffForHumans() }}</td>      
</tr>
@empty
<tr>
  <td colspan="5" class="text-center text-muted py-4">No emails found.</td>
</tr>
@endforelse
