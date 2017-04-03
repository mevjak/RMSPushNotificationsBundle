<?php

namespace RMS\PushNotificationsBundle\Device;

class Types
{
    // amazon device messaging
    const OS_ANDROID_ADM = "rms_push_notifications.os.android.adm";

    // google cloud messaging services
    const OS_ANDROID_C2DM = "rms_push_notifications.os.android.c2dm";
    const OS_ANDROID_GCM = "rms_push_notifications.os.android.gcm";
    const OS_ANDROID_FCM = "rms_push_notifications.os.android.fcm";

    // ios messaging services
    const OS_IOS = "rms_push_notifications.os.ios";
    const OS_MAC = "rms_push_notifications.os.mac";

    // blackberry messaging service
    const OS_BLACKBERRY = "rms_push_notifications.os.blackberry";

    // windows messaging services
    const OS_WINDOWSMOBILE = "rms_push_notifications.os.windowsmobile";
    const OS_WINDOWSPHONE = "rms_push_notifications.os.windowsphone";
}
