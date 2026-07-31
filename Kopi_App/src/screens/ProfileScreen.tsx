import React, { useState } from 'react';
import { View, Text, StyleSheet, SafeAreaView, TextInput, TouchableOpacity } from 'react-native';

export default function ProfileScreen() {
  const [phone, setPhone] = useState('555-1234');
  const name = "Carlos Kopi";
  const matricula = "2021-0001";

  return (
    <SafeAreaView style={styles.safeArea}>
      <View style={styles.container}>
        <Text style={styles.headerTitle}>Mi Perfil</Text>
        
        <View style={styles.fieldContainer}>
          <Text style={styles.label}>Nombre (Solo lectura)</Text>
          <View style={styles.readOnlyInput}>
            <Text style={styles.readOnlyText}>{name}</Text>
          </View>
        </View>
        
        <View style={styles.fieldContainer}>
          <Text style={styles.label}>Matrícula (Solo lectura)</Text>
          <View style={styles.readOnlyInput}>
            <Text style={styles.readOnlyText}>{matricula}</Text>
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

        <TouchableOpacity style={styles.saveButton}>
          <Text style={styles.saveButtonText}>Guardar Cambios</Text>
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
  readOnlyInput: { backgroundColor: '#18181B', padding: 16, borderRadius: 12, borderWidth: 1, borderColor: '#27272A' },
  readOnlyText: { color: '#71717A', fontSize: 16 },
  input: { backgroundColor: '#18181B', padding: 16, borderRadius: 12, borderWidth: 1, borderColor: '#27272A', color: '#FFFFFF', fontSize: 16 },
  saveButton: { backgroundColor: '#FBBF24', paddingVertical: 16, borderRadius: 16, alignItems: 'center', marginTop: 16 },
  saveButtonText: { color: '#09090B', fontSize: 16, fontWeight: 'bold' }
});
