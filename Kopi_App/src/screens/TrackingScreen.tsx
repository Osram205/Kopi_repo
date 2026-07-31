import React from 'react';
import { View, Text, StyleSheet, TouchableOpacity, SafeAreaView } from 'react-native';
import { NativeStackScreenProps } from '@react-navigation/native-stack';
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

  return (
    <SafeAreaView style={styles.safeArea}>
      <View style={styles.container}>
        <Text style={styles.title}>Siguiendo el viaje a {destino}</Text>
        
        <View style={styles.card}>
          <Text style={styles.label}>Conductor:</Text>
          <Text style={styles.value}>{conductor.nombre}</Text>
          
          <View style={{height: 16}} />
          
          <Text style={styles.label}>Vehículo:</Text>
          <Text style={styles.value}>{vehiculo.marca}</Text>
          
          <View style={{height: 16}} />
          
          <Text style={styles.label}>Placa:</Text>
          <Text style={styles.value}>{maskPlaca(vehiculo.placa)}</Text>
        </View>

        <TouchableOpacity 
          style={styles.finishButton}
          onPress={() => navigation.navigate('Rating', route.params)}
        >
          <Text style={styles.finishButtonText}>Finalizar Viaje</Text>
        </TouchableOpacity>
      </View>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  safeArea: { flex: 1, backgroundColor: '#09090B' },
  container: { flex: 1, padding: 24, justifyContent: 'center' },
  title: { fontSize: 24, fontWeight: 'bold', color: '#FFFFFF', marginBottom: 32, textAlign: 'center' },
  card: { backgroundColor: '#18181B', padding: 24, borderRadius: 16, borderWidth: 1, borderColor: '#27272A', marginBottom: 40 },
  label: { fontSize: 14, color: '#A1A1AA', marginBottom: 4 },
  value: { fontSize: 18, color: '#FFFFFF', fontWeight: '500' },
  finishButton: { backgroundColor: '#FBBF24', paddingVertical: 16, borderRadius: 16, alignItems: 'center' },
  finishButtonText: { color: '#09090B', fontSize: 16, fontWeight: 'bold' }
});
