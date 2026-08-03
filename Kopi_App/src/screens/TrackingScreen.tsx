import React from 'react';
import { View, Text, StyleSheet, TouchableOpacity, SafeAreaView } from 'react-native';
import { NativeStackScreenProps } from '@react-navigation/native-stack';
import MapView, { Marker, Polyline } from 'react-native-maps';
import { FeedStackParamList } from '../navigation/types';

type Props = NativeStackScreenProps<FeedStackParamList, 'Tracking'>;

const maskPlaca = (placa: string) => {
  if (!placa) return '***';
  const parts = placa.split('-');
  if (parts.length > 1) {
    return `${parts[0]}-***`;
  }
  return placa.substring(0, 3) + '***';
};

export default function TrackingScreen({ route, navigation }: Props) {
  const { destino, vehiculo, conductor } = route.params;

  // Mocked coordinates for the route
  const centralLocation = { latitude: 20.58806, longitude: -100.38806 };
  const upqLocation = { latitude: 20.5625, longitude: -100.2450 };
  
  const routeCoordinates = [
    upqLocation,
    { latitude: 20.5650, longitude: -100.2800 },
    { latitude: 20.5700, longitude: -100.3200 },
    { latitude: 20.5800, longitude: -100.3500 },
    centralLocation
  ];

  return (
    <View style={styles.container}>
      <MapView
        style={styles.map}
        initialRegion={{
          ...centralLocation,
          latitudeDelta: 0.0922,
          longitudeDelta: 0.0421,
        }}
      >
        <Polyline
          coordinates={routeCoordinates}
          strokeColor="#FBBF24"
          strokeWidth={4}
        />
        <Marker
          coordinate={centralLocation}
          title={conductor.nombre}
          description={`Placa: ${maskPlaca(vehiculo.placa)} - Destino: ${destino}`}
        />
        <Marker
          coordinate={upqLocation}
          title="UPQ"
          description="Universidad Politécnica de Querétaro"
          pinColor="blue"
        />
      </MapView>

      <SafeAreaView style={styles.overlay} pointerEvents="box-none">
        <TouchableOpacity 
          style={styles.backButton}
          onPress={() => navigation.goBack()}
        >
          <Text style={styles.backButtonText}>{'< Volver'}</Text>
        </TouchableOpacity>

        <View style={styles.bottomContainer}>
          <View style={styles.infoCard}>
            <Text style={styles.title}>Viaje a {destino}</Text>
            <Text style={styles.label}>Conductor: <Text style={styles.value}>{conductor.nombre}</Text></Text>
            <Text style={styles.label}>Vehículo: <Text style={styles.value}>{vehiculo.marca} ({maskPlaca(vehiculo.placa)})</Text></Text>
          </View>

          <TouchableOpacity 
            style={styles.finishButton}
            onPress={() => navigation.navigate('Rating', route.params)}
          >
            <Text style={styles.finishButtonText}>Finalizar Viaje</Text>
          </TouchableOpacity>
        </View>
      </SafeAreaView>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#09090B',
  },
  map: {
    ...StyleSheet.absoluteFillObject,
  },
  overlay: {
    flex: 1,
    justifyContent: 'space-between',
  },
  backButton: {
    marginTop: 16,
    marginLeft: 16,
    alignSelf: 'flex-start',
    backgroundColor: 'rgba(9, 9, 11, 0.8)',
    paddingVertical: 10,
    paddingHorizontal: 16,
    borderRadius: 8,
  },
  backButtonText: {
    color: '#FBBF24',
    fontSize: 16,
    fontWeight: 'bold',
  },
  bottomContainer: {
    padding: 20,
  },
  infoCard: {
    backgroundColor: 'rgba(24, 24, 27, 0.95)',
    padding: 16,
    borderRadius: 16,
    borderWidth: 1,
    borderColor: '#27272A',
    marginBottom: 16,
  },
  title: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#FFFFFF',
    marginBottom: 8,
  },
  label: {
    fontSize: 14,
    color: '#A1A1AA',
    marginBottom: 4,
  },
  value: {
    color: '#FFFFFF',
    fontWeight: '500',
  },
  finishButton: {
    backgroundColor: '#FBBF24',
    paddingVertical: 16,
    borderRadius: 16,
    alignItems: 'center',
  },
  finishButtonText: {
    color: '#09090B',
    fontSize: 16,
    fontWeight: 'bold',
  },
});
