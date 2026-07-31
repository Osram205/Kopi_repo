import React from 'react';
import { View, Text, StyleSheet, Image, ScrollView, TouchableOpacity, SafeAreaView, StatusBar } from 'react-native';
import { NativeStackScreenProps } from '@react-navigation/native-stack';
import { FeedStackParamList } from '../navigation/types';

type Props = NativeStackScreenProps<FeedStackParamList, 'FeedDetail'>;

export default function FeedDetailScreen({ route, navigation }: Props) {
  const { destino, imageUrl, precio, description, conductor, vehiculo, paradas } = route.params;

  React.useLayoutEffect(() => {
    navigation.setOptions({
      title: 'Detalle del Viaje',
      headerStyle: { backgroundColor: '#09090B' },
      headerTintColor: '#FBBF24',
      headerShadowVisible: false,
    });
  }, [navigation]);

  return (
    <SafeAreaView style={styles.safeArea}>
      <StatusBar barStyle="light-content" />
      <ScrollView style={styles.container} bounces={false}>
        <Image source={{ uri: imageUrl }} style={styles.heroImage} />
        
        <View style={styles.contentContainer}>
          <View style={styles.headerRow}>
            <View style={{ flex: 1 }}>
              <Text style={styles.title}>{destino}</Text>
              <Text style={styles.location}>Conductor: {conductor.nombre}</Text>
              <Text style={styles.location}>Vehículo: {vehiculo.marca}</Text>
            </View>
            <View style={styles.ratingBadge}>
              <Text style={styles.ratingText}>★ {conductor.calificacion}</Text>
            </View>
          </View>

          <View style={styles.divider} />

          <Text style={styles.sectionTitle}>Descripción</Text>
          <Text style={styles.description}>{description}</Text>
          
          <View style={styles.divider} />
          
          {paradas && paradas.length > 0 && (
            <>
              <Text style={styles.sectionTitle}>Paradas</Text>
              <View style={styles.amenitiesContainer}>
                {paradas.map((parada, index) => (
                  <View key={index} style={styles.amenityTag}>
                    <Text style={styles.amenityText}>{parada}</Text>
                  </View>
                ))}
              </View>
              <View style={styles.divider} />
            </>
          )}

        </View>
      </ScrollView>

      <View style={styles.footer}>
        <View>
          <Text style={styles.priceLabel}>Precio total</Text>
          <Text style={styles.priceValue}>{precio}</Text>
        </View>
        <TouchableOpacity 
          style={styles.bookButton}
          onPress={() => navigation.navigate('Tracking', route.params)}
        >
          <Text style={styles.bookButtonText}>Seguir Viaje</Text>
        </TouchableOpacity>
      </View>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  safeArea: { flex: 1, backgroundColor: '#09090B' },
  container: { flex: 1 },
  heroImage: { width: '100%', height: 250 },
  contentContainer: {
    padding: 24,
    backgroundColor: '#09090B',
    borderTopLeftRadius: 24,
    borderTopRightRadius: 24,
    marginTop: -24,
  },
  headerRow: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'flex-start', marginBottom: 20 },
  title: { fontSize: 24, fontWeight: '800', color: '#FFFFFF', marginBottom: 8 },
  location: { fontSize: 16, color: '#A1A1AA', fontWeight: '500', marginBottom: 4 },
  ratingBadge: { backgroundColor: '#18181B', paddingHorizontal: 12, paddingVertical: 8, borderRadius: 16, borderWidth: 1, borderColor: '#27272A' },
  ratingText: { color: '#FBBF24', fontWeight: 'bold', fontSize: 16 },
  divider: { height: 1, backgroundColor: '#27272A', marginVertical: 24 },
  sectionTitle: { fontSize: 20, fontWeight: '700', color: '#FFFFFF', marginBottom: 12 },
  description: { fontSize: 16, color: '#D4D4D8', lineHeight: 26 },
  amenitiesContainer: { flexDirection: 'row', flexWrap: 'wrap', gap: 12 },
  amenityTag: { backgroundColor: '#18181B', paddingHorizontal: 16, paddingVertical: 8, borderRadius: 20, borderWidth: 1, borderColor: '#27272A' },
  amenityText: { color: '#E5E5E5', fontWeight: '500' },
  footer: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', padding: 24, backgroundColor: '#09090B', borderTopWidth: 1, borderTopColor: '#27272A' },
  priceLabel: { fontSize: 14, color: '#A1A1AA', marginBottom: 4 },
  priceValue: { fontSize: 24, fontWeight: '800', color: '#FFFFFF' },
  bookButton: { backgroundColor: '#FBBF24', paddingHorizontal: 32, paddingVertical: 16, borderRadius: 16 },
  bookButtonText: { color: '#09090B', fontSize: 16, fontWeight: 'bold' }
});
