import React from 'react';
import { NavigationContainer, DarkTheme } from '@react-navigation/native';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import { createNativeStackNavigator } from '@react-navigation/native-stack';

import LoginScreen from '../screens/LoginScreen';
import FeedScreen from '../screens/FeedScreen';
import FeedDetailScreen from '../screens/FeedDetailScreen';
import TrackingScreen from '../screens/TrackingScreen';
import RatingScreen from '../screens/RatingScreen';
import ProfileScreen from '../screens/ProfileScreen';

import { BottomTabParamList, FeedStackParamList, RootStackParamList } from './types';

const Tab = createBottomTabNavigator<BottomTabParamList>();
const FeedStack = createNativeStackNavigator<FeedStackParamList>();
const RootStack = createNativeStackNavigator<RootStackParamList>();

function FeedStackNavigator() {
  return (
    <FeedStack.Navigator 
      screenOptions={{ 
        headerStyle: { backgroundColor: '#09090B' },
        headerTintColor: '#FBBF24',
        headerTitleStyle: { fontWeight: 'bold' },
        headerBackVisible: false,
      }}>
      <FeedStack.Screen 
        name="FeedList" 
        component={FeedScreen} 
        options={{ title: 'Explorar Viajes' }} 
      />
      <FeedStack.Screen 
        name="FeedDetail" 
        component={FeedDetailScreen} 
        options={{ title: 'Detalle del Viaje' }}
      />
      <FeedStack.Screen 
        name="Tracking" 
        component={TrackingScreen} 
        options={{ title: 'Seguimiento de Viaje' }}
      />
      <FeedStack.Screen 
        name="Rating" 
        component={RatingScreen} 
        options={{ title: 'Calificar Viaje', headerBackVisible: false }}
      />
    </FeedStack.Navigator>
  );
}

function MainTabs() {
  return (
    <Tab.Navigator 
      screenOptions={{ 
        headerShown: false,
        tabBarStyle: { backgroundColor: '#09090B', borderTopColor: '#333' },
        tabBarActiveTintColor: '#FBBF24',
        tabBarInactiveTintColor: '#888',
      }}>
      <Tab.Screen 
        name="Feed" 
        component={FeedStackNavigator} 
        options={{ 
          title: 'Viajes',
        }} 
      />
      <Tab.Screen 
        name="Profile" 
        component={ProfileScreen} 
        options={{ 
          title: 'Perfil',
        }} 
      />
    </Tab.Navigator>
  );
}

export default function RootNavigator() {
  return (
    <NavigationContainer theme={DarkTheme}>
      <RootStack.Navigator screenOptions={{ headerShown: false }}>
        <RootStack.Screen name="Login" component={LoginScreen} />
        <RootStack.Screen name="Main" component={MainTabs} />
      </RootStack.Navigator>
    </NavigationContainer>
  );
}
