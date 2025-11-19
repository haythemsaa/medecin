import notifee, { AndroidImportance, EventType } from '@notifee/react-native';

/**
 * Local notifications utilities using Notifee
 */

export const notifications = {
  /**
   * Request notification permission
   */
  requestPermission: async (): Promise<boolean> => {
    const settings = await notifee.requestPermission();
    return settings.authorizationStatus >= 1; // 1 = authorized
  },

  /**
   * Create notification channel (Android)
   */
  createChannel: async () => {
    await notifee.createChannel({
      id: 'default',
      name: 'Default',
      importance: AndroidImportance.HIGH,
    });

    await notifee.createChannel({
      id: 'medication',
      name: 'Rappels de médicaments',
      importance: AndroidImportance.HIGH,
      sound: 'default',
    });

    await notifee.createChannel({
      id: 'appointment',
      name: 'Rendez-vous',
      importance: AndroidImportance.HIGH,
      sound: 'default',
    });
  },

  /**
   * Display local notification
   */
  display: async (options: {
    title: string;
    body: string;
    channelId?: string;
    data?: Record<string, any>;
  }) => {
    await notifee.displayNotification({
      title: options.title,
      body: options.body,
      android: {
        channelId: options.channelId || 'default',
        importance: AndroidImportance.HIGH,
        smallIcon: 'ic_launcher',
      },
      ios: {
        sound: 'default',
      },
      data: options.data,
    });
  },

  /**
   * Schedule medication reminder
   */
  scheduleMedicationReminder: async (options: {
    id: string;
    medicationName: string;
    dosage: string;
    time: Date;
  }) => {
    const trigger = {
      type: 'timestamp' as const,
      timestamp: options.time.getTime(),
      repeatFrequency: 'daily' as const,
    };

    await notifee.createTriggerNotification(
      {
        id: options.id,
        title: '💊 Rappel de médicament',
        body: `Il est temps de prendre ${options.medicationName} (${options.dosage})`,
        android: {
          channelId: 'medication',
          importance: AndroidImportance.HIGH,
          smallIcon: 'ic_launcher',
          pressAction: {
            id: 'default',
          },
        },
        ios: {
          sound: 'default',
          categoryId: 'medication',
        },
        data: {
          type: 'medication',
          medicationName: options.medicationName,
          dosage: options.dosage,
        },
      },
      trigger
    );
  },

  /**
   * Schedule appointment reminder
   */
  scheduleAppointmentReminder: async (options: {
    id: string;
    doctorName: string;
    appointmentTime: Date;
    minutesBefore: number;
  }) => {
    const reminderTime = new Date(options.appointmentTime);
    reminderTime.setMinutes(reminderTime.getMinutes() - options.minutesBefore);

    const trigger = {
      type: 'timestamp' as const,
      timestamp: reminderTime.getTime(),
    };

    await notifee.createTriggerNotification(
      {
        id: options.id,
        title: '📅 Rappel de rendez-vous',
        body: `Vous avez un rendez-vous avec ${options.doctorName} dans ${options.minutesBefore} minutes`,
        android: {
          channelId: 'appointment',
          importance: AndroidImportance.HIGH,
          smallIcon: 'ic_launcher',
          pressAction: {
            id: 'default',
          },
        },
        ios: {
          sound: 'default',
          categoryId: 'appointment',
        },
        data: {
          type: 'appointment',
          doctorName: options.doctorName,
        },
      },
      trigger
    );
  },

  /**
   * Cancel notification
   */
  cancel: async (notificationId: string) => {
    await notifee.cancelNotification(notificationId);
  },

  /**
   * Cancel all notifications
   */
  cancelAll: async () => {
    await notifee.cancelAllNotifications();
  },

  /**
   * Get scheduled notifications
   */
  getScheduled: async () => {
    const notifications = await notifee.getTriggerNotifications();
    return notifications;
  },

  /**
   * Handle notification press
   */
  onNotificationPress: (callback: (notification: any) => void) => {
    return notifee.onForegroundEvent(({ type, detail }) => {
      if (type === EventType.PRESS) {
        callback(detail.notification);
      }
    });
  },
};

export default notifications;
