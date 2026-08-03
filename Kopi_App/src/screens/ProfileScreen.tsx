import React, { useState } from 'react';
import {
  View,
  Text,
  StyleSheet,
  SafeAreaView,
  TextInput,
  TouchableOpacity,
  ActivityIndicator,
} from 'react-native';

import { useAuth } from '../context/AuthContext';
import { useNavigation } from '@react-navigation/native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { RootStackParamList } from '../navigation/types';

export default function ProfileScreen() {
  const { usuario, logout } = useAuth();
  const navigation = useNavigation<NativeStackNavigationProp<RootStackParamList>>();
  const [phone, setPhone] = useState(usuario?.telefono ?? '');
  const [isLoggingOut, setIsLoggingOut] = useState(false);

  const nombreCompleto = usuario
    ? `${usuario.nombre} ${usuario.apellidos ?? ''}`.trim()
    : '—';

  const handleLogout = async () => {
    setIsLoggingOut(true);
    try {
      await logout();
      // RootNavigator vuelve a "Login" automáticamente al cambiar
      // isAuthenticated a false; no hace falta navegar a mano.
    } finally {
      setIsLoggingOut(false);
    }
  };

  return (
    <SafeAreaView style={styles.safeArea}>
      <View style={styles.container}>
        <Text style={styles.headerTitle}>Mi Perfil</Text>

        <View style={styles.fieldContainer}>
          <Text style={styles.label}>Nombre (Solo lectura)</Text>
          <View style={styles.readOnlyInput}>
            <Text style={styles.readOnlyText}>{nombreCompleto}</Text>
          </View>
        </View>

        <View style={styles.fieldContainer}>
          <Text style={styles.label}>Matrícula (Solo lectura)</Text>
          <View style={styles.readOnlyInput}>
            <Text style={styles.readOnlyText}>{usuario?.matricula ?? '—'}</Text>
          </View>
        </View>

        <View style={styles.fieldContainer}>
          <Text style={styles.label}>Teléfono (Editable)</Text>
          <TextInput
            style={styles.input}
            value={phone}
            onChangeText={setPhone}
            keyboardType="phone-pad"
            placeholder="Ingresa tu teléfono"
            placeholderTextColor="#A1A1AA"
          />
        </View>

        {/*
          NOTA: este botón de "Guardar Cambios" queda fuera del alcance
          del Módulo 1 (Autenticación). Conectarlo a POST /usuarios/perfil
          corresponde al módulo de "Perfil" cuando lo trabajemos.
        */}
        <TouchableOpacity style={styles.saveButton}>
          <Text style={styles.saveButtonText}>Guardar Cambios</Text>
        </TouchableOpacity>

        <TouchableOpacity 
          style={styles.addVehicleButton}
          onPress={() => navigation.navigate('AddVehicle')}
        >
          <Text style={styles.addVehicleButtonText}>🚗 Registrar mi Vehículo</Text>
        </TouchableOpacity>

        <TouchableOpacity
          style={styles.logoutButton}
          onPress={handleLogout}
          disabled={isLoggingOut}
          activeOpacity={0.8}
        >
          {isLoggingOut ? (
            <ActivityIndicator color="#FBBF24" />
          ) : (
            <Text style={styles.logoutButtonText}>Cerrar Sesión</Text>
          )}
        </TouchableOpacity>
      </View>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  safeArea: { flex: 1, backgroundColor: '#09090B' },
  container: { flex: 1, padding: 24 },
  headerTitle: { fontSize: 32, fontWeight: '800', color: '#FFFFFF', marginBottom: 32 },
  fieldContainer: { marginBottom: 24 },
  label: { fontSize: 14, color: '#A1A1AA', marginBottom: 8 },
  readOnlyInput: {
    backgroundColor: '#18181B',
    padding: 16,
    borderRadius: 12,
    borderWidth: 1,
    borderColor: '#27272A',
  },
  readOnlyText: { color: '#71717A', fontSize: 16 },
  input: {
    backgroundColor: '#18181B',
    padding: 16,
    borderRadius: 12,
    borderWidth: 1,
    borderColor: '#27272A',
    color: '#FFFFFF',
    fontSize: 16,
  },
  saveButton: {
    backgroundColor: '#FBBF24',
    paddingVertical: 16,
    borderRadius: 16,
    alignItems: 'center',
    marginTop: 16,
  },
  saveButtonText: { color: '#09090B', fontSize: 16, fontWeight: 'bold' },
  logoutButton: {
    paddingVertical: 16,
    borderRadius: 16,
    alignItems: 'center',
    marginTop: 16,
    borderWidth: 1,
    borderColor: '#FBBF24',
  },
  logoutButtonText: { color: '#FBBF24', fontSize: 16, fontWeight: 'bold' },
  addVehicleButton: {
    backgroundColor: '#3F3F46',
    paddingVertical: 16,
    borderRadius: 16,
    alignItems: 'center',
    marginTop: 16,
  },
  addVehicleButtonText: { color: '#FFFFFF', fontSize: 16, fontWeight: 'bold' },
});
