import React from 'react';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import Icon from 'react-native-vector-icons/Ionicons';
import HomeScreen from '../screens/patient/HomeScreen';
import SearchDoctorsScreen from '../screens/patient/SearchDoctorsScreen';
import AppointmentsScreen from '../screens/patient/AppointmentsScreen';
import ProfileScreen from '../screens/patient/ProfileScreen';

export type PatientTabParamList = {
  Home: undefined;
  Search: undefined;
  Appointments: undefined;
  Profile: undefined;
};

const Tab = createBottomTabNavigator<PatientTabParamList>();

export default function PatientNavigator() {
  return (
    <Tab.Navigator
      screenOptions={{
        headerShown: false,
        tabBarActiveTintColor: '#14B8A6',
        tabBarInactiveTintColor: '#9CA3AF',
        tabBarStyle: {
          backgroundColor: '#FFFFFF',
          borderTopWidth: 1,
          borderTopColor: '#E5E7EB',
          height: 60,
          paddingBottom: 8,
          paddingTop: 8,
        },
        tabBarLabelStyle: {
          fontSize: 12,
          fontWeight: '600',
        },
      }}
    >
      <Tab.Screen
        name="Home"
        component={HomeScreen}
        options={{
          tabBarLabel: 'Accueil',
          tabBarIcon: ({ color, size }) => <Icon name="home" size={size} color={color} />,
        }}
      />
      <Tab.Screen
        name="Search"
        component={SearchDoctorsScreen}
        options={{
          tabBarLabel: 'Rechercher',
          tabBarIcon: ({ color, size }) => <Icon name="search" size={size} color={color} />,
        }}
      />
      <Tab.Screen
        name="Appointments"
        component={AppointmentsScreen}
        options={{
          tabBarLabel: 'Rendez-vous',
          tabBarIcon: ({ color, size }) => <Icon name="calendar" size={size} color={color} />,
        }}
      />
      <Tab.Screen
        name="Profile"
        component={ProfileScreen}
        options={{
          tabBarLabel: 'Profil',
          tabBarIcon: ({ color, size }) => <Icon name="person" size={size} color={color} />,
        }}
      />
    </Tab.Navigator>
  );
}
