import React, { useState, useCallback } from 'react';
import { View, Text, StyleSheet, TextInput, TouchableOpacity, ScrollView, SafeAreaView, ActivityIndicator, Alert } from 'react-native';
import { NativeStackScreenProps } from '@react-navigation/native-stack';
import { useFocusEffect } from '@react-navigation/native';
import { RootStackParamList } from '../navigation/types';
import { tripService } from '../services/trip.service';
import { apiClient } from '../api/client';

type Props = NativeStackScreenProps<RootStackParamList, 'PublishTrip'>;

export default function PublishTripScreen({ navigation }: Props) {
  const [origen, setOrigen] = useState('');
  const [destino, setDestino] = useState('');
  const [fechaSalida, setFechaSalida] = useState('');
  const [horaSalida, setHoraSalida] = useState('');
  const [asientos, setAsientos] = useState('3');
  const [costo, setCosto] = useState('1000');
  const [paradas, setParadas] = useState('');
  const [loading, setLoading] = useState(false);
  const [vehicleId, setVehicleId] = useState<number | null>(null);
  const [fetchingVehicles, setFetchingVehicles] = useState(true);

  useFocusEffect(
    useCallback(() => {
      const fetchVehicles = async () => {
        setFetchingVehicles(true);
        try {
          const response = await apiClient.get('/vehiculos/');
          const vehicles = response.data;
          if (vehicles && vehicles.length > 0) {
            setVehicleId(vehicles[0].id);
          } else {
            setVehicleId(null);
            Alert.alert('Aviso', 'Debes registrar un vehículo primero.');
          }
        } catch (error) {
          console.error(error);
        } finally {
          setFetchingVehicles(false);
        }
      };
      fetchVehicles();
    }, [])
  );

  const handlePublish = async () => {
    if (!vehicleId) {
      Alert.alert('Error', 'Debes registrar un vehículo primero.');
      return;
    }
    if (!origen || !destino || !fechaSalida || !horaSalida) {
      Alert.alert('Error', 'Por favor completa los campos obligatorios.');
      return;
    }

    setLoading(true);
    try {
      await tripService.crearViaje({
        origen,
        destino,
        fecha_salida: fechaSalida,
        hora_salida: horaSalida,
        asientos_disponibles: parseInt(asientos, 10),
        costo_por_asiento: parseFloat(costo),
        paradas: paradas ? paradas.split(',').map(p => p.trim()) : [],
        vehiculo_id: vehicleId,
      });
      Alert.alert('Éxito', 'Viaje publicado correctamente.');
      navigation.goBack();
    } catch (error) {
      console.error(error);
      Alert.alert('Error', 'Hubo un problema al publicar el viaje.');
    } finally {
      setLoading(false);
    }
  };

  return (
    <SafeAreaView style={styles.safeArea}>
      <ScrollView style={styles.container} contentContainerStyle={styles.content}>
        <Text style={styles.title}>Publicar Viaje</Text>

        <View style={styles.inputGroup}>
          <Text style={styles.label}>Origen</Text>
          <TextInput style={styles.input} placeholder="Ej: Buenos Aires" placeholderTextColor="#A1A1AA" value={origen} onChangeText={setOrigen} />
        </View>

        <View style={styles.inputGroup}>
          <Text style={styles.label}>Destino</Text>
          <TextInput style={styles.input} placeholder="Ej: Rosario" placeholderTextColor="#A1A1AA" value={destino} onChangeText={setDestino} />
        </View>

        <View style={styles.row}>
          <View style={[styles.inputGroup, { flex: 1, marginRight: 8 }]}>
            <Text style={styles.label}>Fecha (YYYY-MM-DD)</Text>
            <TextInput style={styles.input} placeholder="2026-12-01" placeholderTextColor="#A1A1AA" value={fechaSalida} onChangeText={setFechaSalida} />
          </View>
          <View style={[styles.inputGroup, { flex: 1, marginLeft: 8 }]}>
            <Text style={styles.label}>Hora (HH:MM)</Text>
            <TextInput style={styles.input} placeholder="08:30" placeholderTextColor="#A1A1AA" value={horaSalida} onChangeText={setHoraSalida} />
          </View>
        </View>

        <View style={styles.row}>
          <View style={[styles.inputGroup, { flex: 1, marginRight: 8 }]}>
            <Text style={styles.label}>Asientos</Text>
            <TextInput style={styles.input} keyboardType="numeric" placeholder="3" placeholderTextColor="#A1A1AA" value={asientos} onChangeText={setAsientos} />
          </View>
          <View style={[styles.inputGroup, { flex: 1, marginLeft: 8 }]}>
            <Text style={styles.label}>Precio</Text>
            <TextInput style={styles.input} keyboardType="numeric" placeholder="1000" placeholderTextColor="#A1A1AA" value={costo} onChangeText={setCosto} />
          </View>
        </View>

        <View style={styles.inputGroup}>
          <Text style={styles.label}>Paradas (separadas por coma)</Text>
          <TextInput style={styles.input} placeholder="Ej: Campana, San Nicolás" placeholderTextColor="#A1A1AA" value={paradas} onChangeText={setParadas} />
        </View>

        <TouchableOpacity style={styles.button} onPress={handlePublish} disabled={loading || fetchingVehicles || !vehicleId}>
          {loading ? <ActivityIndicator color="#09090B" /> : <Text style={styles.buttonText}>Publicar</Text>}
        </TouchableOpacity>
      </ScrollView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  safeArea: { flex: 1, backgroundColor: '#09090B' },
  container: { flex: 1 },
  content: { padding: 24 },
  title: { fontSize: 32, fontWeight: '800', color: '#FFFFFF', marginBottom: 24 },
  inputGroup: { marginBottom: 16 },
  row: { flexDirection: 'row', justifyContent: 'space-between' },
  label: { color: '#A1A1AA', fontSize: 14, marginBottom: 8, fontWeight: '500' },
  input: { backgroundColor: '#18181B', borderWidth: 1, borderColor: '#27272A', borderRadius: 12, padding: 16, color: '#FFFFFF', fontSize: 16 },
  button: { backgroundColor: '#FBBF24', padding: 16, borderRadius: 16, alignItems: 'center', marginTop: 24 },
  buttonText: { color: '#09090B', fontSize: 18, fontWeight: 'bold' }
});
