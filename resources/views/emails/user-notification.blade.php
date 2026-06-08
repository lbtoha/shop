{!! $notificationTemplate !!}

@if (!empty($unsubscribeUrl))
    <div style="margin-top: 30px; border-top: 1px solid #e8e5ef; padding-top: 20px; text-align: center;">
        <p style="font-size: 13px; color: #999999; margin: 0 0 10px 0;">
            If you no longer wish to receive these emails, you can unsubscribe at any time.
        </p>
        <a href="{{ $unsubscribeUrl }}"
            style="display: inline-block; padding: 8px 20px; font-size: 13px; color: #999999; text-decoration: underline;">
            Unsubscribe
        </a>
    </div>
@endif
