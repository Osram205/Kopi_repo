import React, { useState } from 'react';
import { View, Text, StyleSheet, TouchableOpacity, SafeAreaView, TextInput } from 'react-native';
import { NativeStackScreenProps } from '@react-navigation/native-stack';
import { FeedStackParamList } from '../navigation/types';

type Props = NativeStackScreenProps<FeedStackParamList, 'Rating'>;

export default function RatingScreen({ route, navigation }: Props) {
  const { conductor } = route.params;
  const [rating, setRating] = useState(5);
  const [comments, setComments] = useState('');

  return (
    <SafeAreaView style={styles.safeArea}>
      <View style={styles.container}>
        <Text style={styles.title}>Califica tu viaje con {conductor.nombre}</Text>
        
        <View style={styles.starsContainer}>
          {[1, 2, 3, 4, 5].map((star) => (
            <TouchableOpacity key={star} onPress={() => setRating(star)}>
              <Text style={[styles.star, star <= rating && styles.starSelected]}>★</Text>
            </TouchableOpacity>
          ))}
        </View>

        <TextInput
          style={styles.input}
          placeholder="Deja un comentario..."
          placeholderTextColor="#A1A1AA"
          multiline
          value={comments}
          onChangeText={setComments}
        />

        <TouchableOpacity 
          style={styles.submitButton}
          onPress={() => navigation.navigate('FeedList')}
        >
          <Text style={styles.submitButtonText}>Enviar Calificación</Text>
        </TouchableOpacity>
      </View>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  safeArea: { flex: 1, backgroundColor: '#09090B' },
  container: { flex: 1, padding: 24, justifyContent: 'center' },
  title: { fontSize: 24, fontWeight: 'bold', color: '#FFFFFF', marginBottom: 32, textAlign: 'center' },
  starsContainer: { flexDirection: 'row', justifyContent: 'center', marginBottom: 32, gap: 16 },
  star: { fontSize: 48, color: '#27272A' },
  starSelected: { color: '#FBBF24' },
  input: { backgroundColor: '#18181B', borderRadius: 12, padding: 16, color: '#FFFFFF', minHeight: 120, textAlignVertical: 'top', borderWidth: 1, borderColor: '#27272A', marginBottom: 32 },
  submitButton: { backgroundColor: '#FBBF24', paddingVertical: 16, borderRadius: 16, alignItems: 'center' },
  submitButtonText: { color: '#09090B', fontSize: 16, fontWeight: 'bold' }
});
