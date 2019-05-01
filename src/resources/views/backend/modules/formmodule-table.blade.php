<table style="width: 100%" border="1" cellpadding="5" cellspacing="0">
  <thead>
      <tr>
        @foreach($data['list_data'] as $field => $value)
          <th>{{ $field }}</th>
        @endforeach
      </tr>
  </thead>
  <tbody>
    @foreach($data['lists'] as $field => $value)
      <tr> 
        @foreach($data['list_data'] as $list_data)
          <td>{{Ongoingcloud\Laravelcrud\Helpers::getRelation($value, $list_data)}}</td>
        @endforeach
      </tr>
    @endforeach   
  </tbody>
</table>