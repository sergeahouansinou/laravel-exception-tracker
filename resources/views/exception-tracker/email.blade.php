<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exception Report</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f6f9;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;color:#2d3748;line-height:1.6;">
<table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f6f9;padding:24px 0;">
<tr><td align="center">
<table width="640" cellpadding="0" cellspacing="0" style="max-width:640px;width:100%;background-color:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.1);">

    {{-- Header --}}
    <tr>
        <td style="background-color:#e53e3e;padding:20px 24px;">
            <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td style="color:#ffffff;font-size:20px;font-weight:700;">
                        &#9888; Exception Detected
                    </td>
                    <td align="right" style="color:rgba(255,255,255,0.8);font-size:13px;">
                        {{ $payload['environment']['app_name'] ?? 'Laravel' }}
                        &middot;
                        {{ strtoupper($payload['environment']['environment'] ?? 'unknown') }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    {{-- Error Summary --}}
    <tr>
        <td style="padding:24px;">
            <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#fff5f5;border:1px solid #fed7d7;border-radius:6px;padding:16px;">
                <tr>
                    <td>
                        <div style="font-size:11px;text-transform:uppercase;letter-spacing:0.05em;color:#c53030;font-weight:600;margin-bottom:4px;">
                            {{ $payload['exception']['class'] ?? 'Exception' }}
                        </div>
                        <div style="font-size:18px;font-weight:700;color:#2d3748;word-break:break-word;">
                            {{ $payload['exception']['message'] ?? 'No message' }}
                        </div>
                        <div style="margin-top:8px;font-size:13px;color:#718096;">
                            <strong>File:</strong> {{ $payload['exception']['file'] ?? 'unknown' }}:{{ $payload['exception']['line'] ?? '0' }}
                            @if(!empty($payload['exception']['code']))
                                &nbsp;&middot;&nbsp;<strong>Code:</strong> {{ $payload['exception']['code'] }}
                            @endif
                        </div>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    {{-- Stack Trace --}}
    @if(!empty($payload['exception']['trace']))
    <tr>
        <td style="padding:0 24px 24px;">
            <div style="font-size:14px;font-weight:700;color:#2d3748;margin-bottom:8px;">Stack Trace</div>
            <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#2d3748;border-radius:6px;overflow:hidden;">
                <tr>
                    <td style="padding:16px;overflow-x:auto;">
                        @foreach($payload['exception']['trace'] as $frame)
                        <div style="font-family:'SFMono-Regular',Consolas,'Liberation Mono',Menlo,monospace;font-size:12px;color:#e2e8f0;padding:3px 0;{{ $loop->first ? 'color:#fc8181;font-weight:600;' : '' }}">
                            <span style="color:#a0aec0;">#{{ $frame['index'] }}</span>
                            {{ $frame['file'] }}:{{ $frame['line'] }}
                            <span style="color:#90cdf4;">{{ $frame['function'] }}</span>
                        </div>
                        @endforeach
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    @endif

    {{-- Request Details --}}
    @if(!empty($payload['request']) && !empty($payload['request']['url']))
    <tr>
        <td style="padding:0 24px 24px;">
            <div style="font-size:14px;font-weight:700;color:#2d3748;margin-bottom:8px;">Request Details</div>
            <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e2e8f0;border-radius:6px;overflow:hidden;">
                <tr style="background-color:#f7fafc;">
                    <td style="padding:10px 14px;font-size:13px;font-weight:600;color:#4a5568;border-bottom:1px solid #e2e8f0;width:120px;">URL</td>
                    <td style="padding:10px 14px;font-size:13px;color:#2d3748;border-bottom:1px solid #e2e8f0;word-break:break-all;">{{ $payload['request']['url'] }}</td>
                </tr>
                <tr>
                    <td style="padding:10px 14px;font-size:13px;font-weight:600;color:#4a5568;border-bottom:1px solid #e2e8f0;">Method</td>
                    <td style="padding:10px 14px;font-size:13px;color:#2d3748;border-bottom:1px solid #e2e8f0;">
                        <span style="background-color:#edf2f7;padding:2px 8px;border-radius:4px;font-weight:600;font-size:12px;">{{ $payload['request']['method'] }}</span>
                    </td>
                </tr>
                <tr style="background-color:#f7fafc;">
                    <td style="padding:10px 14px;font-size:13px;font-weight:600;color:#4a5568;">IP Address</td>
                    <td style="padding:10px 14px;font-size:13px;color:#2d3748;">{{ $payload['request']['ip'] ?? 'unknown' }}</td>
                </tr>
            </table>
        </td>
    </tr>

    {{-- Request Headers --}}
    @if(!empty($payload['request']['headers']))
    <tr>
        <td style="padding:0 24px 24px;">
            <div style="font-size:14px;font-weight:700;color:#2d3748;margin-bottom:8px;">Headers</div>
            <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e2e8f0;border-radius:6px;overflow:hidden;">
                @foreach($payload['request']['headers'] as $key => $value)
                <tr style="{{ $loop->index % 2 === 0 ? 'background-color:#f7fafc;' : '' }}">
                    <td style="padding:6px 14px;font-size:12px;font-weight:600;color:#4a5568;{{ !$loop->last ? 'border-bottom:1px solid #e2e8f0;' : '' }}font-family:monospace;width:200px;">{{ $key }}</td>
                    <td style="padding:6px 14px;font-size:12px;color:#2d3748;{{ !$loop->last ? 'border-bottom:1px solid #e2e8f0;' : '' }}word-break:break-all;">{{ is_string($value) ? $value : json_encode($value) }}</td>
                </tr>
                @endforeach
            </table>
        </td>
    </tr>
    @endif

    {{-- Request Body --}}
    @if(!empty($payload['request']['body']))
    <tr>
        <td style="padding:0 24px 24px;">
            <div style="font-size:14px;font-weight:700;color:#2d3748;margin-bottom:8px;">Request Body</div>
            <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f7fafc;border:1px solid #e2e8f0;border-radius:6px;overflow:hidden;">
                <tr>
                    <td style="padding:12px 14px;">
                        <pre style="margin:0;font-family:'SFMono-Regular',Consolas,'Liberation Mono',Menlo,monospace;font-size:12px;color:#2d3748;white-space:pre-wrap;word-break:break-all;">{{ json_encode($payload['request']['body'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    @endif
    @endif

    {{-- User Info --}}
    @if(!empty($payload['user']) && !empty($payload['user']['id']))
    <tr>
        <td style="padding:0 24px 24px;">
            <div style="font-size:14px;font-weight:700;color:#2d3748;margin-bottom:8px;">Authenticated User</div>
            <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e2e8f0;border-radius:6px;overflow:hidden;">
                <tr style="background-color:#f7fafc;">
                    <td style="padding:10px 14px;font-size:13px;font-weight:600;color:#4a5568;border-bottom:1px solid #e2e8f0;width:120px;">ID</td>
                    <td style="padding:10px 14px;font-size:13px;color:#2d3748;border-bottom:1px solid #e2e8f0;">{{ $payload['user']['id'] }}</td>
                </tr>
                @if(!empty($payload['user']['email']))
                <tr>
                    <td style="padding:10px 14px;font-size:13px;font-weight:600;color:#4a5568;">Email</td>
                    <td style="padding:10px 14px;font-size:13px;color:#2d3748;">{{ $payload['user']['email'] }}</td>
                </tr>
                @endif
            </table>
        </td>
    </tr>
    @endif

    {{-- Environment --}}
    @if(!empty($payload['environment']))
    <tr>
        <td style="padding:0 24px 24px;">
            <div style="font-size:14px;font-weight:700;color:#2d3748;margin-bottom:8px;">Environment</div>
            <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e2e8f0;border-radius:6px;overflow:hidden;">
                <tr style="background-color:#f7fafc;">
                    <td style="padding:8px 14px;font-size:13px;font-weight:600;color:#4a5568;border-bottom:1px solid #e2e8f0;width:140px;">App</td>
                    <td style="padding:8px 14px;font-size:13px;color:#2d3748;border-bottom:1px solid #e2e8f0;">{{ $payload['environment']['app_name'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="padding:8px 14px;font-size:13px;font-weight:600;color:#4a5568;border-bottom:1px solid #e2e8f0;">Environment</td>
                    <td style="padding:8px 14px;font-size:13px;color:#2d3748;border-bottom:1px solid #e2e8f0;">{{ $payload['environment']['environment'] ?? '-' }}</td>
                </tr>
                <tr style="background-color:#f7fafc;">
                    <td style="padding:8px 14px;font-size:13px;font-weight:600;color:#4a5568;border-bottom:1px solid #e2e8f0;">PHP Version</td>
                    <td style="padding:8px 14px;font-size:13px;color:#2d3748;border-bottom:1px solid #e2e8f0;">{{ $payload['environment']['php_version'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="padding:8px 14px;font-size:13px;font-weight:600;color:#4a5568;border-bottom:1px solid #e2e8f0;">Laravel Version</td>
                    <td style="padding:8px 14px;font-size:13px;color:#2d3748;border-bottom:1px solid #e2e8f0;">{{ $payload['environment']['laravel_version'] ?? '-' }}</td>
                </tr>
                <tr style="background-color:#f7fafc;">
                    <td style="padding:8px 14px;font-size:13px;font-weight:600;color:#4a5568;border-bottom:1px solid #e2e8f0;">Server</td>
                    <td style="padding:8px 14px;font-size:13px;color:#2d3748;border-bottom:1px solid #e2e8f0;">{{ $payload['environment']['server'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="padding:8px 14px;font-size:13px;font-weight:600;color:#4a5568;border-bottom:1px solid #e2e8f0;">Timestamp</td>
                    <td style="padding:8px 14px;font-size:13px;color:#2d3748;border-bottom:1px solid #e2e8f0;">{{ $payload['environment']['timestamp'] ?? '-' }}</td>
                </tr>
                <tr style="background-color:#f7fafc;">
                    <td style="padding:8px 14px;font-size:13px;font-weight:600;color:#4a5568;">Request ID</td>
                    <td style="padding:8px 14px;font-size:13px;color:#2d3748;font-family:monospace;font-size:12px;">{{ $payload['environment']['request_id'] ?? '-' }}</td>
                </tr>
            </table>
        </td>
    </tr>
    @endif

    {{-- Footer --}}
    <tr>
        <td style="padding:16px 24px;background-color:#f7fafc;border-top:1px solid #e2e8f0;text-align:center;font-size:12px;color:#a0aec0;">
            Generated by <strong>Laravel Exception Tracker</strong> &middot; {{ $payload['environment']['timestamp'] ?? now()->toIso8601String() }}
        </td>
    </tr>

</table>
</td></tr>
</table>
</body>
</html>
