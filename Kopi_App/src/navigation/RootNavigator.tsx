import React from 'react';
import { ActivityIndicator, StyleSheet, View } from 'react-native';
import { NavigationContainer, DarkTheme } from '@react-navigation/native';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import { createNativeStackNavigator } from '@react-navigation/native-stack';

import LoginScreen from '../screens/LoginScreen';
import FeedScreen from '../screens/FeedScreen';
import FeedDetailScreen from '../screens/FeedDetailScreen';
import TrackingScreen from '../screens/TrackingScreen';
import RatingScreen from '../screens/RatingScreen';
import ProfileScreen from '../screens/ProfileScreen';
import PublishTripScreen from '../screens/PublishTripScreen';
import AddVehicleScreen from '../screens/AddVehicleScreen';

import { BottomTabParamList, FeedStackParamList, RootStackParamList } from './types';
import { useAuth } from '../context/AuthContext';

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
        options={{ headerShown: false }}
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
        name="PublishTrip"
        component={PublishTripScreen}
        options={{
          title: 'Publicar',
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

/**
 * Pantalla que se muestra mientras se restaura la sesión (SecureStore
 * + validación contra GET /usuarios/perfil). Mantiene el mismo fondo
 * y acento de color que el resto de la app para que no se sienta como
 * un "flash" fuera de estilo.
 */
function LoadingScreen() {
  return (
    <View style={styles.loadingContainer}>
      <ActivityIndicator size="large" color="#FBBF24" />
    </View>
  );
}

export default function RootNavigator() {
  const { isAuthenticated, isLoading } = useAuth();

  if (isLoading) {
    return <LoadingScreen />;
  }

  return (
    <NavigationContainer theme={DarkTheme}>
      <RootStack.Navigator screenOptions={{ headerShown: false }}>
        {isAuthenticated ? (
          // Protección de rutas: mientras no haya sesión, la pantalla
          // "Main" ni siquiera se monta en el stack de navegación.
          <>
            <RootStack.Screen name="Main" component={MainTabs} />
            <RootStack.Screen name="AddVehicle" component={AddVehicleScreen} options={{ presentation: 'modal' }} />
          </>
        ) : (
          <RootStack.Screen name="Login" component={LoginScreen} />
        )}
      </RootStack.Navigator>
    </NavigationContainer>
  );
}

const styles = StyleSheet.create({
  loadingContainer: {
    flex: 1,
    backgroundColor: '#09090B',
    justifyContent: 'center',
    alignItems: 'center',
  },
});
