import React, { useState } from 'react';
import { View, Text, StyleSheet, TextInput, TouchableOpacity, ScrollView, SafeAreaView, ActivityIndicator, Alert } from 'react-native';
import { NativeStackScreenProps } from '@react-navigation/native-stack';
import { RootStackParamList } from '../navigation/types';
import { apiClient } from '../api/client';

type Props = NativeStackScreenProps<RootStackParamList, 'AddVehicle'>;

export default function AddVehicleScreen({ navigation }: Props) {
  const [placas, setPlacas] = useState('');
  const [marca, setMarca] = useState('');
  const [modelo, setModelo] = useState('');
  const [color, setColor] = useState('');
  const [asientos, setAsientos] = useState('');
  const [loading, setLoading] = useState(false);

  const handleSubmit = async () => {
    if (!placas || !marca || !modelo || !color || !asientos) {
      Alert.alert('Error', 'Por favor completa todos los campos.');
      return;
    }

    setLoading(true);
    try {
      await apiClient.post('/vehiculos/', {
        placas,
        marca,
        modelo,
        color,
        asientos: parseInt(asientos, 10),
      });
      Alert.alert('Éxito', 'Vehículo registrado correctamente.');
      navigation.goBack();
    } catch (error) {
      console.error(error);
      Alert.alert('Error', 'Hubo un problema al registrar el vehículo.');
    } finally {
      setLoading(false);
    }
  };

  return (
    <SafeAreaView style={styles.safeArea}>
      <ScrollView style={styles.container} contentContainerStyle={styles.content}>
        <Text style={styles.title}>Registrar Vehículo</Text>

        <View style={styles.inputGroup}>
          <Text style={styles.label}>Placas</Text>
          <TextInput style={styles.input} placeholder="Ej: ABC-123" placeholderTextColor="#A1A1AA" value={placas} onChangeText={setPlacas} autoCapitalize="characters" />
        </View>

        <View style={styles.inputGroup}>
          <Text style={styles.label}>Marca</Text>
          <TextInput style={styles.input} placeholder="Ej: Toyota" placeholderTextColor="#A1A1AA" value={marca} onChangeText={setMarca} />
        </View>

        <View style={styles.inputGroup}>
          <Text style={styles.label}>Modelo</Text>
          <TextInput style={styles.input} placeholder="Ej: Corolla 2022" placeholderTextColor="#A1A1AA" value={modelo} onChangeText={setModelo} />
        </View>

        <View style={styles.inputGroup}>
          <Text style={styles.label}>Color</Text>
          <TextInput style={styles.input} placeholder="Ej: Blanco" placeholderTextColor="#A1A1AA" value={color} onChangeText={setColor} />
        </View>

        <View style={styles.inputGroup}>
          <Text style={styles.label}>Asientos Disponibles</Text>
          <TextInput style={styles.input} keyboardType="numeric" placeholder="Ej: 4" placeholderTextColor="#A1A1AA" value={asientos} onChangeText={setAsientos} />
        </View>

        <TouchableOpacity style={styles.button} onPress={handleSubmit} disabled={loading}>
          {loading ? <ActivityIndicator color="#09090B" /> : <Text style={styles.buttonText}>Registrar Vehículo</Text>}
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
  label: { color: '#A1A1AA', fontSize: 14, marginBottom: 8, fontWeight: '500' },
  input: { backgroundColor: '#18181B', borderWidth: 1, borderColor: '#27272A', borderRadius: 12, padding: 16, color: '#FFFFFF', fontSize: 16 },
  button: { backgroundColor: '#FBBF24', padding: 16, borderRadius: 16, alignItems: 'center', marginTop: 24 },
  buttonText: { color: '#09090B', fontSize: 18, fontWeight: 'bold' }
});
