<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ __('Login Log Report') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            margin: 0 auto;
            background-color: white;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
        }

        success-badge {
            color: white;
            background-color: green;
            padding: 5px;
            border-radius: 100%;
        }

        warning-badge {
            color: white;
            background-color: orange;
            padding: 5px;
            border-radius: 100%;
        }

        danger-badge {
            color: white;
            background-color: red;
            padding: 5px;
            border-radius: 100%;
        }

        secondary-badge {
            color: white;
            background-color: gray;
            padding: 5px;
            border-radius: 100%;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>{{_('Login Log Report')}}</h2>
        <table>
            <thead>
                <tr>
                    <th>{{ __('User') }}</th>
                    <th>{{ __('Login At') }}</th>
                    <th>{{ __("Ip Address") }}</th>
                    <th>{{ __('Location') }}</th>
                    <th>{{ __('Browser|OS') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $item)
                    <tr>
                        <td>{{ $item->user->full_name.'<br>'.$item->user->username }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->created_at)->format('Y-m-d H:i:s').' <br> '.\Carbon\Carbon::parse($item->created_at)->diffForHumans()}}</td>
                        <td>{{ $item->ip_address }}</td>
                        <td>{{ $item->type }}</td>
                        <td>{{ $item->location }}</td>
                        <td>{{ $item->browser.'<br>'.$item->os }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>

</html>
