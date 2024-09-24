<table>
    <tr></tr>
    <tr>
        <td>{{ $arr_data['sheet_head'] }}</td>
    </tr>
    <tr></tr>
    <tr>
        <th>{{translation('sr_no')}}</th>
        <th>{{translation('name')}}</th>
        <th>{{translation('national_id')}}</th>
        <th>{{translation('average_notation')}}</th>
        <th>{{translation('comment')}}</th>
    </tr>
    <?php echo $arr_data['data']; ?>
</table>
